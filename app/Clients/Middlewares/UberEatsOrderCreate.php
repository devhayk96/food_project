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
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isEmpty;

class UberEatsOrderCreate extends AbstractOrderMiddleware
{
    use HasCliUser;

    protected ?OrderInTransit $inTransit = null;

    protected User $user;

    public function __construct()
    {
        $this->stepNumber = 6;
        $this->stepName = 'Order Create';
        $this->logChannel = 'ubereats';

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
        $ubereats = OrderSourceType::where('code', 'ubereats')->firstOrFail();

//        courier type is not defined, we will get only order type : DELIVERY_BY_RESTAURANT
        $courierType = CourierType::where([
            'code' => $this->inTransit->raw['type'],
            'source_type_id' => $ubereats->id
        ])->firstOrFail()->getPrimitive();

        $orderType = OrderType::where([
            'code' => $this->inTransit->raw['type'],
            'source_type_id' => $ubereats->id
        ])->firstOrFail()->getPrimitive();


        $customer = Customer::firstOrCreate(
            [
                'phone' => $this->inTransit->raw['eater']['phone'],
                'name' =>$this->inTransit->raw['eater']['first_name'].' '.$this->inTransit->raw['eater']['last_name'],
                'company' => isset($this->inTransit->raw['eater']['companyName'])
                    ? $this->inTransit->raw['eater']['companyName']
                    : null,
            ], [
                'created_by_id' => $this->user->id,
                'updated_by_id' => $this->user->id
            ]
        );

        if($this->inTransit->raw['eater']['delivery']['location']['type'] === 'GOOGLE_PLACE' && config('app.google_api_key') !== null) {
            $response = Http::get("https://maps.googleapis.com/maps/api/place/details/json?key=" . config('app.google_api_key') . "&place_id=" . $this->inTransit->raw['eater']['delivery']['location']['google_place_id']);
            if ($response->status() === 200) {
                $address = $response->json()['result']['address_components'];

                $street_number =  '';
                $route = '';
                $street_extra = null;
                $city= null;
                $country= null;
                $postcode= '';

                $longitude = $response->json()['result']['geometry']['location']['lng'];
                $latitude = $response->json()['result']['geometry']['location']['lat'];

                foreach ($address as $addr) {
                    if(array_search("street_number",$addr['types']) !== false){
                        $street_number = $addr['long_name'];
                    }
                    if(array_search("route",$addr['types']) !== false){
                        $route = $addr['long_name'];
                    }
                    /*if(array_search("sublocality",$addr['types']) !== false){
                        $street_extra = $addr['long_name'];
                    }*/
                    if(array_search("locality",$addr['types']) !== false){
                        $city = $addr['long_name'];
                    }
                    if(array_search("country",$addr['types']) !== false){
                        $country = $addr['long_name'];
                    }
                    if(array_search("postal_code",$addr['types']) !== false){
                        $postcode = $addr['long_name'];
                    }
                }

                /*$street_number =  $address[0]['long_name'];
                $route = $address[1]['long_name'];
                $street_extra = $address[2]['long_name'];
                $city= $address[3]['long_name'];
                $country= $address[6]['long_name'];
                $postcode= $address[7]['long_name'];*/

                $deliveryAddress = Address::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'street' => $route.' '.$street_number,
                        'postcode' => $postcode,
                        'city' => $city,
                        'country' => $country,
                        'street_extra' => $street_extra,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                    ],
                    [
                        'created_by_id' => $this->user->id,
                        'updated_by_id' => $this->user->id
                    ]
                );
            }
            else{
                $deliveryAddress = Address::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'street' => isset($this->inTransit->raw['eater']['delivery']['type'])?$this->inTransit->raw['eater']['delivery']['type']:'',
                        'postcode' => isset($this->inTransit->raw['eater']['delivery']['postcode'])?$this->inTransit->raw['eater']['delivery']['postcode']:'',
                        'city' => isset($this->inTransit->raw['eater']['delivery']['type'])?$this->inTransit->raw['eater']['delivery']['type']:null,
                    ],
                    [
//                'street_extra' => $this->inTransit->raw['customer']['extraAddressInfo'],
                        'created_by_id' => $this->user->id,
                        'updated_by_id' => $this->user->id
                    ]
                );
            }
        }
        else{
            $deliveryAddress = Address::firstOrCreate(
                [
                    'customer_id' => $customer->id,
                    'street' => isset($this->inTransit->raw['eater']['delivery']['type'])?$this->inTransit->raw['eater']['delivery']['type']:'',
                    'postcode' => isset($this->inTransit->raw['eater']['delivery']['postcode'])?$this->inTransit->raw['eater']['delivery']['postcode']:'',
                    'city' => isset($this->inTransit->raw['eater']['delivery']['type'])?$this->inTransit->raw['eater']['delivery']['type']:null,
                ],
                [
//                'street_extra' => $this->inTransit->raw['customer']['extraAddressInfo'],
                    'created_by_id' => $this->user->id,
                    'updated_by_id' => $this->user->id
                ]
            );
        }

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
            'code' => $this->inTransit->raw['type'],
            'source_type_id' => $ubereats->id
        ])->firstOrFail()->getPrimitive();

        /*$wDay = WorkDay::getLastWorkDayPerShop($shop);
        $wDay->orders = $wDay->orders + 1;
        $wDay->save();*/

        $dat = explode(' ', $this->inTransit->properties['order_datetime'], 2)[0];
        $order_count = Order::where('shop_id', '=', $shop->id)
            ->whereDate('order_datetime', '=', $dat)
            ->selectRaw('count(*) as order_no')
            ->first();

        $order = Order::create([
//            'order_number' => $this->inTransit->raw['id'],
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
            'delivery_remarks' => $this->inTransit->raw['eater']['delivery']['notes'],
            'payment_method_id' => $paymentMethod->id,
            'is_asap' => $this->inTransit->properties['is_asap'],
            'requested_time' => $this->inTransit->properties['requested_time'],
            'order_datetime' => $this->inTransit->properties['order_datetime'],
//            'requested_time' => $this->inTransit->raw['estimated_ready_for_pickup_at'],
//            'order_datetime' => $this->inTransit->raw['placed_at'],
//            'requested_products' => serialize($this->flattenProducts()),
//            'requested_discounts' => serialize($this->flattenDiscounts()),
            'address_id' => $deliveryAddress->id,
            'fulfillment_issues' => isEmpty($this->inTransit->raw['cart']['fulfillment_issues'])?null:json_encode($this->inTransit->raw['cart']['fulfillment_issues']),
            'order_remark' => isset($this->inTransit->raw['cart']['special_instructions'])?$this->inTransit->raw['cart']['special_instructions']:null,
//            'total_discount' => $this->inTransit->raw['totalDiscount'],
            'total_price' => $this->inTransit->raw['payment']['charges']['total']['amount']/100,
            'is_paid' => isset($this->inTransit->raw['isPaid'])?$this->inTransit->raw['isPaid']: 0,
            'tip_price' => $this->inTransit->raw['payment']['charges']['total']['amount']/100 - $this->inTransit->raw['payment']['charges']['sub_total']['amount']/100,
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
        foreach ($this->inTransit->raw['cart']['items'] as $product) {
            $flattened[] = [
                'pos_id' => $product['id'],
                'name' => $product['title'],
                'item_number' => $product['quantity'],
//                'price' => $this->poshubLocale->formatPrice($product['price']),
                'price' => $product['price']['unit_price']['amount']/100,
                'product_remark' => isset($product['special_instructions'])?$product['special_instructions']:null,
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
                        'product_remark' => null,
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
