<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use App\Exceptions\OrderMiddlewareException;

class UberEatsDateTimeFormat extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 5;
        $this->stepName = 'Date Time Format';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        try {
            $orderInTransit->properties['order_datetime'] =
                $this->poshubLocale->getStringForThuisbezorgdDtf($orderInTransit->raw['placed_at']);

            $orderInTransit->properties['requested_time'] = ($orderInTransit->raw['type'] === 'delivery')
                ? $orderInTransit->raw['estimated_ready_for_pickup_at']
                : $orderInTransit->raw['estimated_ready_for_pickup_at'];

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
