<?php


namespace App\Clients\Middlewares;


use App\Entities\OrderInTransit;
use App\Exceptions\OrderAlreadyImportedException;
use App\Exceptions\OrderMiddlewareException;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class UberEatsAlreadyImportedCheck extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 2;
        $this->stepName = 'Already Imported Check';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    /**
     * @param  OrderInTransit                $orderInTransit
     * @return OrderInTransit
     * @throws OrderAlreadyImportedException
     * @throws OrderMiddlewareException
     */
    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        try {
//            $order = Order::where('ubereats_id', $orderInTransit->raw['id'])->firstOrFail();
            $order = Order::whereJsonContains('order_json', ['id'=> $orderInTransit->raw['id']])->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return $orderInTransit;
        } catch (\Throwable $exception) {
            $message = $this->createOrderInErrorAndLogThrowable($exception, $orderInTransit);
            throw new OrderMiddlewareException($message);
        }
        Log::channel($this->logChannel)->info($this->getStep() . ': already imported with ID ' . $order->id);
        throw new OrderAlreadyImportedException();
    }
}
