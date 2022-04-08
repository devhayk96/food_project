<?php


namespace App\Clients\Middlewares;


use App\Entities\OrderInTransit;

class UberEatsEarlyStructuralChecks extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 1;
        $this->stepName = 'Early structural checks';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        $this->validateOrder($orderInTransit, [
            'id' => 'required|string|min:1|max:256',
            'current_state' => 'required|string|in:CREATED',
            'placed_at' => 'required|date_format:Y-m-d\TH:i:s\Z',
        ]);
        return $orderInTransit;
    }
}
