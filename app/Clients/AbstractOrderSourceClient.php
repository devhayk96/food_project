<?php

namespace App\Clients;

use App\AbstractInitializable;
use App\Clients\Middlewares\AbstractOrderMiddleware;
use App\Entities\AbstractChangeStatusRequest;
use App\Entities\OrderInTransit;
use App\Entities\AbstractOrderSourceCredentials;
use App\Exceptions\OrderAlreadyImportedException;
use App\Exceptions\OrderMiddlewareException;
use App\Exceptions\OrderSourceClientException;
use App\HasCliUser;
use App\Locale\PoshubLocale;
use App\Models\Order;
use App\Models\OrderSourceShop;
use App\Models\Shop;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

abstract class AbstractOrderSourceClient extends AbstractInitializable
{
    use HasCliUser;

    /**
     * @var OrderInTransit[]
     */
    public array $orders = [];

    public AbstractOrderSourceCredentials $credentials;

    protected OrderSourceShop $source;

    protected Shop $shop;

    protected PendingRequest $client;

    /**
     * @var AbstractOrderMiddleware[]
     */
    protected array $stack;

    protected PoshubLocale $locale;

    abstract public static function make(array $values = []) : AbstractOrderSourceClient;

    /**
     * Set the $client with the appropriate credentials
     * set the validatorStack
     *
     * @param  OrderSourceShop               $source
     * @param  Shop                      $shop
     * @return AbstractOrderSourceClient
     */
    abstract public function initClient(
        OrderSourceShop $source,
        Shop $shop
    ): AbstractOrderSourceClient;

    /**
     * Download the raw orders and set in the property $orders as an array of OrderInTransit
     */
    abstract public function getOrdersInTransit(): AbstractOrderSourceClient;

    abstract public function acceptOrder(Order $order, array $additionalInfo = []): bool;

    public function processOrdersInTransit() : AbstractOrderSourceClient
    {
        foreach ($this->orders as $orderInTransit) {
            try {
                Log::info('Processing new order in transit from external source.');
                $orderInTransit->properties['shop_id'] = $this->shop->id;
                $orderInTransit->properties['order_source_id'] = $this->source->id;
                $orderInTransit->properties['order_source_type_id'] = $this->source->order_source_type_id;
                $middleware = $this->stack;
                $first = array_shift($middleware);
                $orderInTransit = $first->processAndExecuteNext($orderInTransit, $middleware);
                Log::info('Order successfully processed and imported with id: ' . $orderInTransit->order->id);
            } catch (OrderMiddlewareException|OrderAlreadyImportedException $exception) {
                continue;
            }
        }
        return $this;
    }

    /**
     * @param  Response                   $response
     * @param  int                        $successStatus
     * @return bool
     * @throws OrderSourceClientException
     */
    protected function evaluateResponse(Response $response, int $successStatus = 0): bool
    {
        $status = $response->status();

        if ($response->successful() === true) {
            if ($status === $successStatus || $successStatus === 0) {
                return true;
            }
        }

        if ($response->serverError() === true) {
            throw new OrderSourceClientException(
                "AbstractOrderSourceClient::evaluateResponse server error: " .
                $status . " - " . $response->body(),
                2
            );
        }

        if ($response->clientError() === true) {
            throw new OrderSourceClientException(
                "AbstractOrderSourceClient::evaluateResponse client error: " .
                $status . " - " . $response->body(),
                3
            );
        }

        throw new OrderSourceClientException(
            "AbstractOrderSourceClient::evaluateResponse unknown error: " . $status . " - " . $response->body(),
            4
        );
    }
}
