<?php

namespace App\Clients;

use App\Clients\Middlewares\UberEatsAlreadyImportedCheck;
use App\Clients\Middlewares\UberEatsAutoAcceptOrder;
use App\Clients\Middlewares\UberEatsDateTimeFormat;
use App\Clients\Middlewares\UberEatsEarlyStructuralChecks;
use App\Clients\Middlewares\UberEatsGetOrderDetails;
use App\Clients\Middlewares\UberEatsOrderCreate;
use App\Clients\Middlewares\UberEatsValidateOrderDetails;
use App\Entities\AbstractOrderSourceCredentials;
use App\Entities\OrderInTransit;
use App\Entities\UberEatsCredentials;
use App\Entities\UberEatsToken;
use App\Entities\UberEatsWallet;
use App\Exceptions\EntityException;
use App\Exceptions\OrderMiddlewareException;
use App\Exceptions\OrderSourceClientException;
use App\Exceptions\OrderSourceClientValidationException;
use App\Locale\PoshubLocale;
use App\Models\OrderSourceShop;
use App\Models\Shop;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exceptions\LocaleException;


class UberEatsClient extends AbstractOrderSourceClient
{
    public const LOG_CHANNEL = 'ubereats';

    public const NEW = 'printed';

    public const ACCEPTED = 'accept_pos_order';

    public const DECLINED = 'deny_pos_order';

    public const KITCHEN = 'kitchen';

    public const DELIVERY = 'in_delivery';

    public const FINISHED = 'delivered';

    public const ERROR = 'error';

    public const CANCEL = 'cancel';

    protected array $stack = [
        UberEatsEarlyStructuralChecks::class,
        UberEatsAlreadyImportedCheck::class,
        UberEatsGetOrderDetails::class,
        UberEatsValidateOrderDetails::class,
        UberEatsDateTimeFormat::class,
        UberEatsOrderCreate::class,
        UberEatsAutoAcceptOrder::class
    ];

    protected ?string $tokenUrl = null;

    protected ?string $tokenGrantType = null;

    protected ?string $apiBaseUrl = null;

    protected ?string $orderCreatedUri = null;

    protected ?string $orderCreatedUriSuffix = null;

    protected ?string $orderCreatedLimit = null;

    protected ?string $orderDetailsUri = null;

    protected ?string $orderStatusUri = null;

    protected $uberToken;

    public static function make(array $values = []): AbstractOrderSourceClient
    {
        return new UberEatsClient($values);
    }

    /**
     * @param  OrderSourceShop                $source
     * @param  Shop                       $shop
     * @return AbstractOrderSourceClient
     * @throws OrderSourceClientException
     */
    public function initClient(OrderSourceShop $source, Shop $shop): AbstractOrderSourceClient
    {
        Log::channel(self::LOG_CHANNEL)->info('Client Initialization: Started');
        //generate token
        $this->tokenUrl = config('app.ubereats_token_url', null);
        $this->tokenGrantType = config('app.ubereats_token_grant_type', null);
        $this->apiBaseUrl = config('app.ubereats_api_baseurl', null);
        $this->orderCreatedUri = config('app.ubereats_order_created_uri', null);
        $this->orderCreatedUriSuffix = config('app.ubereats_order_created_uri_suffix', null);
        $this->orderCreatedLimit = config('app.ubereats_order_created_limit', null);
        $this->orderDetailsUri = config('app.ubereats_order_details_uri', null);
        $this->orderStatusUri = config('app.ubereats_order_status_uri', null);
        if (is_null($this->tokenUrl) ||
            is_null($this->tokenGrantType) ||
            is_null($this->apiBaseUrl) ||
            is_null($this->orderCreatedUri) ||
            is_null($this->orderCreatedUriSuffix) ||
            is_null($this->orderCreatedLimit)
        ) {
            throw new OrderSourceClientException("UberEats variable environment not correctly set.", 2);
        }
        $cred = unserialize($source->credentials);
        $response = Http::asForm()->post($this->tokenUrl, [
            'client_secret' => $cred['clientSecret'],
            'client_id' => $cred['clientId'],
            'grant_type' => $this->tokenGrantType,
            'scope' => 'eats.order eats.report eats.store eats.store.orders.cancel eats.store.orders.read eats.store.status.read eats.store.status.write eats.store.orders.restaurantdelivery.status',
        ]);
        $this->evaluateResponse($response);
        $token = UberEatsToken::makeFromResponse($response->json(), false);
        $this->uberToken = $token;
//        $jsonString = json_encode($array);
//        $object = json_decode(json_encode($cred));
        $this->uberToken->credentials = json_decode(json_encode($cred));
        $this->stack = array_map(fn($pointer) => new $pointer(), $this->stack);
        $this->source = $source;
        $this->shop = $shop;
        $this->locale = new PoshubLocale();
        Log::channel(self::LOG_CHANNEL)->info('Client Initialization: Completed');
        return $this;
    }

    /**
     * @param  string $scope
     * @param  bool $autoRenew
     * @throws OrderSourceClientException
     * @throws EntityException
     */
    public function generateToken(string $scope, bool $autoRenew = false): void
    {
        Log::channel(self::LOG_CHANNEL)->info('Generate Token: Started');
        UberEatsWallet::checkScope($scope);

        $response = Http::asForm()->post($this->tokenUrl, [
            'client_secret' => $this->credentials->clientSecret,
            'client_id' => $this->credentials->clientId,
            'grant_type' => $this->tokenGrantType,
            'scope' => $scope
        ]);

        $this->evaluateResponse($response);

        $token = UberEatsToken::makeFromResponse($response->json(), $autoRenew);

        $this->credentials->wallet->setToken($token->scope, $token);

        Log::channel(self::LOG_CHANNEL)->info('Generate Token: Completed');
    }

    /**
     * @return AbstractOrderSourceClient
     * @throws OrderMiddlewareException
     * @throws OrderSourceClientException
     */
    public function getOrdersInTransit(): AbstractOrderSourceClient
    {
        Log::channel(self::LOG_CHANNEL)->info('Pulling Raw Orders: Started');
        /**
         * @var UberEatsToken $token
         */
//        $token = $this->credentials->wallet->getToken('eats.store.orders.read');
        $token = $this->uberToken;

        if (empty($token->accessToken)) {
            Log::channel(self::LOG_CHANNEL)->info(
                'Pulling Raw Orders: Completed because token is empty'
            );
            throw new OrderMiddlewareException('Token is empty');
        }

        $response = Http::withHeaders([
            'authorization' => 'Bearer ' . $token->accessToken
        ])->get(
            $this->apiBaseUrl . $this->orderCreatedUri . "/" . $token->credentials->restaurantId .
            $this->orderCreatedUriSuffix . "?limit=" . $this->orderCreatedLimit
        );

        $this->evaluateResponse($response, 200);

        $this->orders = array_map(
            fn($rawOrder) => OrderInTransit::make(['raw' => $rawOrder]),
            $response->json()['orders']
        );

        $orderNumbers = count($this->orders);
        Log::channel(self::LOG_CHANNEL)->info('Pulling Raw Orders: Found ' . $orderNumbers . ' orders');
        Log::channel(self::LOG_CHANNEL)->info('Pulling Raw Orders: Completed');
        return $this;
    }

    /**
     * @param  string                     $orderId
     * @return array
     * @throws OrderMiddlewareException
     * @throws OrderSourceClientException
     */
    public function getOrderDetails(string $orderId): array
    {
        Log::channel(self::LOG_CHANNEL)->info('Get order details: Started for id ' . $orderId);
        /**
         * @var UberEatsToken $token
         */
//        $token = $this->credentials->wallet->getToken('eats.store.orders.read');
        $token = $this->uberToken;

        if (empty($token->accessToken)) {
            Log::channel(self::LOG_CHANNEL)->info(
                'Pulling Raw Orders: Completed because token is empty'
            );
            throw new OrderMiddlewareException('Token is empty');
        }

        $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
            ->get($this->apiBaseUrl . $this->orderDetailsUri . "/" . $orderId);

        $this->evaluateResponse($response, 200);
        Log::channel(self::LOG_CHANNEL)->info('Get order details: Successfully completed.');
        return $response->json();
    }

    public function acceptOrder(Order $order, array $additionalInfo = []): bool
    {
        Log::channel(self::LOG_CHANNEL)->info('Accepting order id: ' . $order->id . ' STARTED');

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
            $token = $this->uberToken;
            $orderId = json_decode($order->order_json)->id;
            $data['reason'] = 'accepted';
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/' . self::ACCEPTED, $data);
            $this->evaluateResponse($response, 204);
            Log::channel(self::LOG_CHANNEL)->info('Accepting order id: ' . $order->id . ' COMPLETED');
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }


    public function deliveryOrder(Order $order): bool
    {
        if(json_decode($order->order_json)->type != 'DELIVERY_BY_RESTAURANT'){
            return true;
        }
        Log::channel(self::LOG_CHANNEL)->info('Moving order to Delivery, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $orderId = json_decode($order->order_json)->id;
            $data['status'] = 'arriving';
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/restaurantdelivery/status', $data);
            $this->evaluateResponse($response, 200);
            Log::channel(self::LOG_CHANNEL)->info(
                'Delivering the order, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    public function finishOrder(Order $order): bool
    {
        if(json_decode($order->order_json)->type != 'DELIVERY_BY_RESTAURANT'){
            return true;
        }
        Log::channel(self::LOG_CHANNEL)->info('Moving order to Finish, id: ' . $order->id . ' STARTED');
        try {
            $token = $this->uberToken;
            $orderId = json_decode($order->order_json)->id;
            $data['status'] = self::FINISHED;
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/restaurantdelivery/status', $data);
            $this->evaluateResponse($response, 200);
            Log::channel(self::LOG_CHANNEL)->info(
                'Finishing the order, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    public function declineOrder(Order $order): bool
    {
        Log::channel(self::LOG_CHANNEL)->info('Moving order to Decline, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $orderId = json_decode($order->order_json)->id;
            $data['reason']['code'] = 'OTHER';
            $data['reason']['explanation'] = 'Unknown.';
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/' . self::DECLINED, $data);
            $this->evaluateResponse($response, 204);
            Log::channel(self::LOG_CHANNEL)->info(
                'Denying the order, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    public function cancelOrder(Order $order): bool
    {
        Log::channel(self::LOG_CHANNEL)->info('Moving order to Cancel, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $orderId = json_decode($order->order_json)->id;
            $data['reason'] = 'OTHER';
            $data['details'] = 'Unknown.';
            $data['cancelling_party'] = 'MERCHANT';
            $aaa = $this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/' . self::CANCEL;
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . $this->orderStatusUri . "/" . $orderId . '/' . self::CANCEL, $data);
            $this->evaluateResponse($response, 200);
            Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );
            return true;
        } catch (OrderSourceClientException $exception) {
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            throw $exception;
        }
    }

    public function getStoreStatus(OrderSourceShop $source)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->get($this->apiBaseUrl . "/v1/eats/store/" . $store_id . '/status');
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            return $response->json();
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            return $exception->getMessage();
        }
    }

    public function setStoreStatus(OrderSourceShop $source, $status): bool
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $data['status'] = $status;
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . "/v1/eats/store/" . $store_id . '/status', $data);
            $a = $response->json();
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            return true;
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            return $exception->getMessage();
        }
    }


    public function getStoreMenu(OrderSourceShop $source)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->get($this->apiBaseUrl . "/v2/eats/stores/" . $store_id . '/menus');
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            return $response->json();
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            return $exception->getMessage();
        }
    }

    public function setStoreMenu(OrderSourceShop $source, $menu)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->put($this->apiBaseUrl . "/v2/eats/stores/" . $store_id . '/menus', $menu);
            $this->evaluateResponse($response, 204);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            $data['menu'] = $menu;
            $data['status'] = true;
            $data['response'] = $response->json();
            return $data;
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            $data['menu'] = $menu;
            $data['status'] = false;
            $data['response'] = $exception->getMessage();
            return $data;
        }
    }


    public function getHolidayHours(OrderSourceShop $source)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->get($this->apiBaseUrl . "/v1/eats/stores/" . $store_id . '/holiday-hours');
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            return $response->json();
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            return $exception->getMessage();
        }
    }

    public function setHolidayHours(OrderSourceShop $source, $holidayHours)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->post($this->apiBaseUrl . "/v1/eats/stores/" . $store_id . '/holiday-hours', $holidayHours);
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            $data['holidayHours'] = $holidayHours;
            $data['status'] = true;
            $data['response'] = $response->json();
            return $data;
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            $data['holidayHours'] = $holidayHours;
            $data['status'] = false;
            $data['response'] = $exception->getMessage();
            return $data;
        }
    }

    public function getStoreDetails(OrderSourceShop $source)
    {
//        Log::channel(self::LOG_CHANNEL)->info('Moving order to kitchen, id: ' . $order->id . ' STARTED');

        try {
            $token = $this->uberToken;
            $store_id = unserialize($source->credentials)['restaurantId'];
            $response = Http::withHeaders(['authorization' => 'Bearer ' . $token->accessToken])
                ->get($this->apiBaseUrl . "/v1/eats/stores/" . $store_id);
            $this->evaluateResponse($response, 200);
            /*Log::channel(self::LOG_CHANNEL)->info(
                'Cancelling the order, id: ' . $order->id . ' COMPLETED'
            );*/
            return $response->json();
        } catch (OrderSourceClientException $exception) {
            /*Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );*/
            return $exception->getMessage();
        }
    }

}
