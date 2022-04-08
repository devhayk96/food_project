<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use App\Exceptions\OrderMiddlewareException;

class ThuisbezorgdDateTimeFormat extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 4;
        $this->stepName = 'Date Time Format';
        $this->logChannel = 'thuisbezorgd';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        try {
            $orderInTransit->properties['order_datetime'] =
                $this->poshubLocale->getStringForThuisbezorgdDtf($orderInTransit->raw['orderDate']);

            $orderInTransit->properties['requested_time'] = ($orderInTransit->raw['orderType'] === 'delivery')
                ? $orderInTransit->raw['requestedDeliveryTime']
                : $orderInTransit->raw['requestedPickupTime'];

            if (mb_strtoupper($orderInTransit->properties['requested_time']) === 'ASAP') {
                $orderInTransit->properties['is_asap'] = true;
                $orderInTransit->properties['requested_time'] = null;
                return $orderInTransit;
            }

            $orderInTransit->properties['is_asap'] = false;
            $orderInTransit->properties['requested_time'] = $this->poshubLocale->getStringForThuisbezorgdDtf(
                $orderInTransit->properties['requested_time']
            );
            return $orderInTransit;
        } catch (\Throwable $exception) {
            $message = $this->createOrderInErrorAndLogThrowable($exception, $orderInTransit);
            throw new OrderMiddlewareException($message);
        }
    }
}
