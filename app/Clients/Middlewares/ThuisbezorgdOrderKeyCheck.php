<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;

class ThuisbezorgdOrderKeyCheck extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 1;
        $this->stepName = 'Order Key validation';
        $this->logChannel = 'thuisbezorgd';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        $this->validateOrder($orderInTransit, ['orderKey' => 'required|min:3|max:256']);
        return $orderInTransit;
    }
}
