<?php

namespace App\Clients\Middlewares;

use App\Clients\UberEatsClient;
use App\Entities\OrderInTransit;
use App\Exceptions\OrderMiddlewareException;
use App\Exceptions\OrderSourceClientException;
use App\Models\OrderSource;
use App\Models\OrderSourceShop;
use App\Models\Shop;

class UberEatsGetOrderDetails extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 3;
        $this->stepName = 'Get order details';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    /**
     * @param  OrderInTransit             $orderInTransit
     * @return OrderInTransit
     * @throws OrderMiddlewareException
     * @throws OrderSourceClientException
     */
    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        $shop = Shop::findOrFail($orderInTransit->properties['shop_id']);
        $source = OrderSourceShop::findOrFail($orderInTransit->properties['order_source_id']);
        /**
         * @var UberEatsClient $client
         */
        $client = UberEatsClient::make()->initClient($source, $shop);
        $orderRaw = $client->getOrderDetails($orderInTransit->raw['id']);
        $orderInTransit->raw = $orderRaw;

//        file_put_contents('/var/www/html/ubereats.json', json_encode($orderRaw, JSON_PRETTY_PRINT));

        return $orderInTransit;
    }
}
