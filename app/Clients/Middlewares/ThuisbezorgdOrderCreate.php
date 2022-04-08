<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use App\Exceptions\OrderMiddlewareException;
use App\HasCliUser;
use App\Locale\PoshubLocale;
use App\Models\Address;
use App\Models\CourierType;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderSourceType;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ThuisbezorgdOrderCreate extends AbstractOrderMiddleware
{
    use HasCliUser;

    protected ?OrderInTransit $inTransit = null;

    protected User $user;

    public function __construct()
    {
        $this->stepNumber = 5;
        $this->stepName = 'Order Create';
        $this->logChannel = 'thuisbezorgd';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        try {
            $this->user = $this->getCurrentUserOrCli();
            $this->inTransit = $orderInTransit;
            DB::beginTransaction();
            $this->inTransit->order = $this->createOrderFromOrderInTransit();
            DB::commit();
            return $this->inTransit;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $message = $this->getStep() . ': UNEXPECTED ERROR';
            if (isset($this->inTransit)) {
                $message = $this->createOrderInErrorAndLogThrowable($exception, $this->inTransit);
            }
            $this->logThrowable($exception, $message);
            throw new OrderMiddlewareException($message);
        }
    }

    protected function createOrderFromOrderInTransit(): Order
    {
        $pos = OrderSourceType::where('code', 'pos')->firstOrFail();
        $thuisbezorgd = OrderSourceType::where('code', 'thuisbezorgd')->firstOrFail();

        $courierType = CourierType::where([
            'code' => $this->inTransit->raw['courier'],
            'source_type_id' => $thuisbezorgd->id
        ])->firstOrFail()->getPrimitive();

        $orderType = OrderType::where([
            'code' => $this->inTransit->raw['orderType'],
            'source_type_id' => $thuisbezorgd->id
        ])->firstOrFail()->getPrimitive();


        $customer = Customer::firstOrCreate(
            [
                'phone' => $this->inTransit->raw['customer']['phoneNumber'],
                'name' =>$this->inTransit->raw['customer']['name'],
                'company' => isset($this->inTransit->raw['customer']['companyName'])
                    ? $this->inTransit->raw['customer']['companyName']
                    : null,
            ], [
                'created_by_id' => $this->user->id,
                'updated_by_id' => $this->user->id
            ]
        );

        $deliveryAddress = Address::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'street' => $this->getStreet(),
                'postcode' => $this->inTransit->raw['customer']['postalCode'],
                'city' => $this->inTransit->raw['customer']['city'],
                'street_extra' => $this->inTransit->raw['customer']['extraAddressInfo'],
            ],
            [
                'created_by_id' => $this->user->id,
                'updated_by_id' => $this->user->id
            ]
        );

        /*$delivery = Delivery::create([
            'courier_type_id' => $courierType->id,
            'delivery_costs' => isset($this->inTransit->raw['deliveryCosts'])
                ? $this->inTransit->raw['deliveryCosts'] : null,
            'delivery_remark' => $this->inTransit->raw['customer']['extraAddressInfo'],
            'address_id' => $deliveryAddress->id,
            'created_by_id' => $this->user->id,
            'updated_by_id' => $this->user->id
        ]);*/

        $shop = Shop::findOrFail($this->inTransit->properties['shop_id']);

        $orderStatus = OrderStatus::where([
            'code' => OrderStatus::NEW,
            'source_type_id' => $pos->id
        ])->firstOrFail();

        $paymentMethod = PaymentMethod::where([
            'code' => $this->inTransit->raw['paymentMethod'],
            'source_type_id' => $thuisbezorgd->id
        ])->firstOrFail()->getPrimitive();

       /* $wDay = WorkDay::getLastWorkDayPerShop($shop);
        $wDay->orders = $wDay->orders + 1;
        $wDay->save();*/

        $dat = explode(' ', $this->inTransit->properties['order_datetime'], 2)[0];
        $order_count = Order::where('shop_id', '=', $shop->id)
            ->whereDate('order_datetime', '=', $dat)
            ->selectRaw('count(*) as order_no')
            ->first();

        $order = Order::create([
//            'order_number' => $this->inTransit->raw['orderKey'],
            'daily_order_number' => $order_count->order_no+1,
//            'daily_order_number' => (int)$wDay->orders,
            'order_status_id' => $orderStatus->id,
            'order_source_id' => $this->inTransit->properties['order_source_id'],
            'order_type_id' => $orderType->id,
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'courier_type_id' => $courierType->id,
            'delivery_cost' => isset($this->inTransit->raw['deliveryCosts'])
                ? $this->inTransit->raw['deliveryCosts'] : 0,
            'payment_method_id' => $paymentMethod->id,
            'is_asap' => $this->inTransit->properties['is_asap'],
            'requested_time' => $this->inTransit->properties['requested_time'],
            'order_datetime' => $this->inTransit->properties['order_datetime'],
//            'requested_products' => serialize($this->flattenProducts()),
//            'requested_discounts' => serialize($this->flattenDiscounts()),
            'address_id' => $deliveryAddress->id,
            'pays_with' => isset($this->inTransit->raw['paysWith'])?$this->inTransit->raw['paysWith']:null,
            'order_remark' => $this->inTransit->raw['remark'],
            'total_discount' => $this->inTransit->raw['totalDiscount'],
            'total_price' => $this->inTransit->raw['totalPrice'],
            'is_paid' => $this->inTransit->raw['isPaid'],
            'order_json' => json_encode($this->inTransit->raw),
            'customer_json' => json_encode($customer),
            'address_json' => json_encode($deliveryAddress),
            'created_by_id' => $this->user->id,
            'updated_by_id' => $this->user->id
        ]);
        $product_list = $this->flattenProducts();
        $total_vat_amount = 0;
        $delivery_vat_amount = 0;
        foreach ($product_list as $product){
            $our_product = Product::where('article_number', $product['pos_id'])->firstOrFail();
//            product calculation
            $total_price = (float)$product['price']*(int)$product['item_number'];
            $vat_amount = $our_product->vats === null? 0:($total_price/100)*(float)$our_product->vats->percentage;
//            order calculation
            $total_vat_amount = $total_vat_amount + $vat_amount;
            $delivery_vat_amount = $delivery_vat_amount + 0;

            OrderProduct::create([
                'product_id'=> $our_product->id,
                'vat_id'=> $our_product->vat_id,
                'order_id'=> $order->id,
                'quantity'=> $product['item_number'],
                'unit_price'=> (float)$product['price'],
                'total_price'=> $total_price,
                'vat_amount'=> $vat_amount,
                'remarks'=> $product['product_remark'],
                'created_by_id' => $this->user->id,
                'updated_by_id' => $this->user->id
            ]);
        }

        $order->total_vat_amount = $total_vat_amount;
        $order->delivery_vat_amount = $delivery_vat_amount;

        return $order;
    }

    protected function getStreet() : string
    {
        return $this->inTransit->raw['customer']['street'] . ' ' . $this->inTransit->raw['customer']['streetNumber'];
    }

    protected function flattenProducts() : array
    {
        $flattened = [];
        foreach ($this->inTransit->raw['products'] as $product) {
            $flattened[] = [
                'pos_id' => $product['id'],
                'name' => $product['name'],
                'item_number' => $product['count'],
//                'price' => $this->poshubLocale->formatPrice($product['price']),
                'price' => $product['price'],
                'product_remark' => isset($product['remark'])?$product['remark']:null,
                'is_optional' => false,
            ];

            if (!empty($product['sideDishes'])) {
                foreach ($product['sideDishes'] as $sideDish) {
                    $flattened[] = [
                        'pos_id' => $sideDish['id'],
                        'name' => $sideDish['name'],
                        'item_number' => $sideDish['count'],
                        'price' => $sideDish['price'],
//                        'price' => $this->poshubLocale->formatPrice($sideDish['price']),
                        'product_remark' => isset($sideDish['remark'])?$sideDish['remark']:null,
                        'is_optional' => true,
                    ];
                }
            }
        }
        return $flattened;
    }

    protected function flattenDiscounts()
    {
        return array_reduce(
            $this->inTransit->raw['discounts'],
            function(array $carry, array $discount) {
                $carry[] = [
                    'code' => $discount['name'],
                    'item_number' => $discount['count'],
                    'value' => $this->poshubLocale->formatPrice($discount['price']),
                ];
                return $carry;
            },
            []
        );
    }
}
