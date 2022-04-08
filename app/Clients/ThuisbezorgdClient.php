<?php

namespace App\Clients;

use App\Clients\Middlewares\ThuisbezorgdAlreadyImportedCheck;
use App\Clients\Middlewares\ThuisbezorgdAutoAcceptOrder;
use App\Clients\Middlewares\ThuisbezorgdDateTimeFormat;
use App\Clients\Middlewares\ThuisbezorgdOrderCreate;
use App\Clients\Middlewares\ThuisbezorgdOrderKeyCheck;
use App\Clients\Middlewares\ThuisbezorgdStructuralCheck;
use App\Entities\OrderInTransit;
use App\Entities\ThuisbezorgdCredentials;
use App\Exceptions\LocaleException;
use App\Exceptions\OrderSourceClientException;
use App\Exceptions\OrderSourceClientValidationException;
use App\Locale\PoshubLocale;
use App\Models\Order;
use App\Models\OrderSourceShop;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThuisbezorgdClient extends AbstractOrderSourceClient
{
    public const NEW = 'printed';

    public const ACCEPTED = 'confirmed_change_delivery_time';

    public const DECLINED = 'error';

    public const KITCHEN = 'kitchen';

    public const DELIVERY = 'in_delivery';

    public const FINISHED = 'delivered';

    public const ERROR = 'error';

    protected array $stack = [
        ThuisbezorgdOrderKeyCheck::class,
        ThuisbezorgdAlreadyImportedCheck::class,
        ThuisbezorgdStructuralCheck::class,
        ThuisbezorgdDateTimeFormat::class,
        ThuisbezorgdOrderCreate::class,
        ThuisbezorgdAutoAcceptOrder::class
    ];

    protected string $baseUrl;

    protected string $version;

    protected string $ordersUri;

    protected string $statusUri;

    public static function make(array $values = []): AbstractOrderSourceClient
    {
        return new ThuisbezorgdClient($values);
    }

    /**
     * @param  OrderSourceShop               $source
     * @param  Shop                      $shop
     * @return $this
     * @throws OrderSourceClientException
     */
    public function initClient(OrderSourceShop $source, Shop $shop): AbstractOrderSourceClient
    {
        Log::channel('thuisbezorgd')->info('Client Initialization: Started');
        $this->credentials = ThuisbezorgdCredentials::make()->initFromSerialized($source->credentials);

        $this->stack = array_map(fn($pointer) => new $pointer(), $this->stack);

        $this->source = $source;

        $this->client = Http::withBasicAuth($this->credentials->username, $this->credentials->password)
            ->withHeaders(['Apikey' => $this->credentials->apiKey]);

        $this->shop = $shop;

        $this->baseUrl = config('app.thuisbezorgd_baseurl', null);
        $this->version = config('app.thuisbezorgd_version', null);
        $this->ordersUri = config('app.thuisbezorgd_order_uri', null);
        $this->statusUri = config('app.thuisbezorgd_status_uri', null);
        $this->locale = new PoshubLocale();

        if (is_null($this->baseUrl) ||
            is_null($this->version) ||
            is_null($this->ordersUri) ||
            is_null($this->statusUri)) {
            throw new OrderSourceClientException("Thuisbezorgd variable environment not set.", 1);
        }

        Log::channel('thuisbezorgd')->info('Client Initialization: Completed');
        return $this;
    }

    /**
     * @return ThuisbezorgdClient
     * @throws OrderSourceClientException
     */
    public function getOrdersInTransit(): AbstractOrderSourceClient
    {
        Log::channel('thuisbezorgd')->info('Pulling Raw Orders: Started');
        $response = $this->client->get($this->getUrl($this->ordersUri));

        $this->evaluateResponse($response, 200);

        $this->orders = array_map(
            fn($rawOrder) => OrderInTransit::make(['raw' => $rawOrder]),
            $response->json()['orders']
        );

        $orderNumbers = count($this->orders);
        Log::channel('thuisbezorgd')->info('Pulling Raw Orders: Found ' . $orderNumbers . ' orders');
        Log::channel('thuisbezorgd')->info('Pulling Raw Orders: Completed');
        return $this;
    }

    /**
     * @param  Order $order
     * @param  array $additionalInfo
     * @return bool
     * @throws OrderSourceClientValidationException|OrderSourceClientException|LocaleException
     */
    public function acceptOrder(Order $order, array $additionalInfo = []): bool
    {
        Log::channel('thuisbezorgd')->info('Accepting order id: ' . $order->id . ' STARTED');

        $changedDeliveryTime = $this->locale->getCarbonFromLocale($additionalInfo['accepted_datetime']);

        $orderDateTime = $this->locale->getCarbonFromSystemToLocaleTz($order->order_datetime);

        $orderDateTime->addMinutes(5);
        if ($changedDeliveryTime->greaterThan($orderDateTime) === false) {
            throw new OrderSourceClientValidationException(
                'The accepted datetime is not at least 5 minute after the order date',
                1
            );
        }

        $orderDateTime->subMinutes(5);
        $orderDateTime->addDay();
        $orderDateTime->hour(7);
        $orderDateTime->minute(0);
        $orderDateTime->second(0);

        if ($changedDeliveryTime->greaterThan($orderDateTime) === true) {
            throw new OrderSourceClientValidationException(
                'The accepted datetime is greater than the 7am of the next day or the order date',
                2
            );
        }

        try {
            $response = $this->client->post(
                $this->getUrl($this->statusUri),
                /*[
                    'id' => $order->thuisbezorgd_id,
                    'key' => $order->thuisbezorgd_order_key,
                    'status' => self::ACCEPTED,
                    'changedDeliveryTime' => $changedDeliveryTime->format('c')
                ]*/
                [
                    'id' => json_decode($order->order_json)->id,
                    'key' => json_decode($order->order_json)->orderKey,
                    'status' => self::ACCEPTED,
                    'changedDeliveryTime' => $changedDeliveryTime->format('c')
                ]
            );
            $this->evaluateResponse($response, 200);
            Log::channel('thuisbezorgd')->info('Accepting order id: ' . $order->id . ' COMPLETED');
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel('thuisbezorgd')->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @param  Order $order
     * @return bool
     * @throws OrderSourceClientException
     */
    public function kitchenOrder(Order $order): bool
    {
        Log::channel('thuisbezorgd')->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $response = $this->client->post(
                $this->getUrl($this->statusUri),
               /* [
                    'id' => $order->thuisbezorgd_id,
                    'key' => $order->thuisbezorgd_order_key,
                    'status' => self::KITCHEN
                ]*/
                [
                    'id' => json_decode($order->order_json)->id,
                    'key' => json_decode($order->order_json)->orderKey,
                    'status' => self::KITCHEN
                ]
            );
            $this->evaluateResponse($response, 200);
            Log::channel('thuisbezorgd')->info(
                'Moving order to kitchen, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel('thuisbezorgd')->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @param  Order $order
     * @return bool
     * @throws OrderSourceClientException
     */
    public function deliveryOrder(Order $order): bool
    {
        Log::channel('thuisbezorgd')->info('Moving order to delivery, id: ' . $order->id . ' STARTED');

        try {
            $response = $this->client->post(
                $this->getUrl($this->statusUri),
                [
                    'id' => json_decode($order->order_json)->id,
                    'key' => json_decode($order->order_json)->orderKey,
                    'status' => self::DELIVERY
                ]
                /*[
                    'id' => $order->thuisbezorgd_id,
                    'key' => $order->thuisbezorgd_order_key,
                    'status' => self::DELIVERY
                ]*/
            );
            $this->evaluateResponse($response, 200);
            Log::channel('thuisbezorgd')->info(
                'Moving order to delivery, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel('thuisbezorgd')->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @param  Order $order
     * @return bool
     * @throws OrderSourceClientException
     */
    public function finishOrder(Order $order): bool
    {
        Log::channel('thuisbezorgd')->info('Moving order to finished, id: ' . $order->id . ' STARTED');

        try {
            $response = $this->client->post(
                $this->getUrl($this->statusUri),
                [
                    'id' => json_decode($order->order_json)->id,
                    'key' => json_decode($order->order_json)->orderKey,
                    'status' => self::FINISHED
                ]
                /*[
                    'id' => $order->thuisbezorgd_id,
                    'key' => $order->thuisbezorgd_order_key,
                    'status' => self::FINISHED
                ]*/
            );
            $this->evaluateResponse($response, 200);
            Log::channel('thuisbezorgd')->info(
                'Moving order to finished, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel('thuisbezorgd')->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @param  Order                                $order
     * @param  array                                $additionalInfo
     * @return bool
     * @throws OrderSourceClientValidationException|OrderSourceClientException
     */
    public function declineOrder(Order $order, array $additionalInfo = []): bool
    {
        return $this->errorOrder($order, $additionalInfo);
    }

    /**
     * @param  Order                                $order
     * @param  array                                $additionalInfo
     * @return bool
     * @throws OrderSourceClientValidationException|OrderSourceClientException
     */
    public function errorOrder(Order $order, array $additionalInfo = []): bool
    {
        Log::channel('thuisbezorgd')->info('Move order to error, id: ' . $order->id . ' STARTED');

        try {
            $response = $this->client->post(
                $this->getUrl($this->statusUri),
                [
                    'id' => json_decode($order->order_json)->id,
                    'key' => json_decode($order->order_json)->orderKey,
                    'status' => self::ACCEPTED,
                    'text' => $additionalInfo['message']
                ]
                /*[
                    'id' => $order->thuisbezorgd_id,
                    'key' => $order->thuisbezorgd_order_key,
                    'status' => self::ACCEPTED,
                    'text' => $additionalInfo['message']
                ]*/
            );
            $this->evaluateResponse($response, 200);
            Log::channel('thuisbezorgd')->info('Move order to error, id: ' . $order->id . ' COMPLETED');
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel('thuisbezorgd')->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    protected function getUrl(string $method): string
    {
        return $this->baseUrl . '/' .
            $this->version . '/' .
            $method . '/' .
            $this->credentials->restaurantId;
    }
}
