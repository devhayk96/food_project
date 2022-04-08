<?php


namespace App\Clients\Middlewares;


use App\Entities\OrderInTransit;
use App\Entities\ThuisbezorgdChangeStatusRequest;
use App\Exceptions\OrderMiddlewareException;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\Shop;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UberEatsAutoAcceptOrder extends AbstractOrderMiddleware
{
    protected OrderInTransit $inTransit;

    protected Shop $shop;

    public function __construct()
    {
        $this->stepNumber = 7;
        $this->stepName = 'Auto Accept Order';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        try {
            if ($orderInTransit->order->orderSource->is_auto_accept === 0) {
                Log::channel($this->logChannel)->info("Auto accept order is disabled for this source.");
                return $orderInTransit;
            }

            $this->inTransit = $orderInTransit;

            if ($this->inTransit->order->orderStatus->code != OrderStatus::NEW) {
                Log::channel($this->logChannel)->info(
                    "OrderStatus is not new, code is " . $this->inTransit->order->orderStatus->code
                );
                return $orderInTransit;
            }

            $this->shop = Shop::findOrFail($this->inTransit->properties['shop_id']);
            $date = $this->getAcceptedCarbon();
            $this->inTransit->order->orderSource->orderSourceType->client_class::make()
                ->initClient($this->inTransit->order->orderSource, $this->shop)
                ->acceptOrder(
                    $this->inTransit->order,
                    [
                        'accepted_datetime' => $date->setTimezone($this->poshubLocale->poshubLocaleTz)
                            ->format($this->poshubLocale->poshubLocaleDtFormat)
                    ]
                );

            $accepted = OrderStatus::where([
                'code' => OrderStatus::ACCEPTED,
                'parent_id' => null
            ])->firstOrFail();

            $this->inTransit->order->order_status_id = $accepted->id;

            $this->inTransit->order->requested_time = $date
                ->setTimezone($this->poshubLocale->poshubSystemTz)
                ->format($this->poshubLocale->poshubSystemDtFormat);

            $this->inTransit->order->save();
            $this->inTransit->order->refresh();
            return $this->inTransit;
        } catch (\Throwable $exception) {
            $message = $this->getStep() . ': UNEXPECTED ERROR';
            if (isset($this->inTransit)) {
                $message = $this->createOrderInErrorAndLogThrowable($exception, $this->inTransit);
            }
            $this->logThrowable($exception, $message);
            throw new OrderMiddlewareException($message);
        }
    }

    protected function getAcceptedCarbon(): Carbon
    {
        $now = Carbon::now();
        if ($this->inTransit->order->is_asap === true) {
            $minutes = ($this->inTransit->order->orderType->code === OrderType::DELIVERY)
                ? $this->shop->delivery_time
                : $this->shop->pickup_time;
            $now->addMinutes($minutes);
            return $now;
        }

        return $this->poshubLocale->getCarbonFromSystem($this->inTransit->order->requested_time);

    }
}
