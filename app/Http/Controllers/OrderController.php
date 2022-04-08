<?php

namespace App\Http\Controllers;

use App\Console\Commands\OrdersPull;
use App\Console\Commands\ShopsPulse;
use App\Http\Resources\KDS\OrderKDSCollection;
use App\Http\Resources\OrderErrorResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\Pos\OrderPosCollection;
use App\Models\Address;
use App\Models\CancellationReason;
use App\Models\CourierType;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Kitchen;
use App\Models\OrderDiscount;
use App\Models\OrderInError;
use App\Models\OrderProduct;
use App\Models\OrderSourceShop;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\PaymentMethod;
use App\Models\Device;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductSubgroup;
use App\Models\Vat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class OrderController extends Controller
{
    /**
     * Store Validation rule preparation
     **/

    protected array $createConstrains = [
        'order_number' => ['nullable', 'numeric'],
        'order_status_id' => ['required', 'exists:poshub_order_statuses,code'],
        'order_source_id' => ['required', 'exists:poshub_order_source_types,code'],
        'order_type_id' => ['required', 'exists:poshub_order_types,code'],
        'shop_id' => ['required', 'exists:poshub_shops,id'],
        'courier_type_id' => ['required', 'exists:poshub_courier_types,code'],
        'table_id' => ['nullable', 'sometimes', 'exists:poshub_tables,id'],
        'cancellation_id' => ['nullable', 'sometimes', 'exists:poshub_cancellation_reasons,code'],
        'cancellation_comment' => ['nullable', 'sometimes'],
        'delivery_cost' => ['nullable', 'numeric'],
        'delivery_vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'delivery_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'delivery_remarks' => ['nullable', 'sometimes', 'string'],
        'payment_method_id' => ['required', 'exists:poshub_payment_methods,code'],
        'payment_session_id' => ['nullable', 'sometimes'],
        'is_asap' => ['nullable', 'boolean'],
        'requested_time' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'order_datetime' => ['required', 'date_format:Y-m-d H:i:s'],
        'total_discount' => ['nullable', 'numeric'],
        'total_price' => ['required', 'numeric'],
        'pays_with' => ['nullable', 'sometimes', 'numeric'],
        'extra_costs' => ['nullable', 'sometimes', 'numeric'],
        'total_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'extra_cost_vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'extra_cost_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'tip_amount' => ['nullable', 'sometimes', 'numeric'],
        'is_paid' => ['nullable', 'boolean'],
        'is_pos_sync' => ['nullable', 'boolean'],
        'order_remark' => ['nullable', 'sometimes'],
        'number_of_guests' => ['nullable', 'sometimes', 'numeric'],
        'course' => ['nullable', 'sometimes', 'string'],
        'fulfillment_issues' => ['nullable', 'sometimes'],
        'distance' => ['nullable', 'sometimes', 'numeric'],
        'invoice_id' => ['nullable', 'sometimes', 'string'],
        'courier_left_at' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'courier_back_at' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'kitchen_id' => ['nullable', 'sometimes', 'exists:poshub_kitchens,id'],
        'courier_id' => ['nullable', 'sometimes'],
        'is_discount_percentage' => ['nullable', 'sometimes', 'boolean'],

        'customer' => ['required'],
        'customer.name' => ['required', 'string'],
        'customer.company_name' => ['nullable', 'sometimes', 'string'],
        'customer.phone_number' => ['nullable'],
        'customer.email' => ['nullable', 'sometimes', 'string'],

        'customer.street' => ['nullable', 'string'],
        'customer.street_extra' => ['nullable', 'sometimes', 'string'],
        'customer.postal_code' => ['nullable'],
        'customer.city' => ['nullable', 'string'],
        'customer.country' => ['nullable', 'sometimes', 'string'],
        'customer.longitude' => ['nullable', 'sometimes', 'string'],
        'customer.latitude' => ['nullable', 'sometimes', 'string'],

        'products' => ['required'],

        'products.*.id' => ['required', 'string'],
        'products.*.name' => ['required', 'string'],
        'products.*.price' => ['required', 'numeric'],
        'products.*.quantity' => ['required', 'numeric'],
        'products.*.remarks' => ['nullable'],
        'products.*.is_optional' => ['nullable', 'sometimes', 'boolean'],
        'products.*.vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'products.*.vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'products.*.discount' => ['nullable', 'sometimes', 'numeric'],
        'products.*.is_discount_percentage' => ['nullable', 'sometimes', 'boolean'],
        'products.*.discounted_amount' => ['nullable', 'sometimes', 'numeric'],

        'discounts' => ['nullable', 'sometimes'],
        'discounts.*.name' => ['nullable', 'sometimes'],
        'discounts.*.count' => ['nullable', 'sometimes', 'numeric'],
        'discounts.*.discount' => ['required', 'numeric'],
		'piggy_stage' => ['nullable'],
		'piggy_physical_reward' => ['nullable'],

    ];

    /**
     * Update Validation rule preparation
     **/

    protected array $updateConstrains = [
        'order_id' => ['required', 'string', 'exists:poshub_orders,id'],
        'order_number' => ['nullable', 'sometimes', 'numeric'],
        'order_status_id' => ['nullable', 'sometimes', 'exists:poshub_order_statuses,code'],
        'order_source_id' => ['nullable', 'sometimes', 'exists:poshub_order_source_types,code'],
        'order_type_id' => ['nullable', 'sometimes', 'exists:poshub_order_types,code'],
        'shop_id' => ['nullable', 'sometimes', 'exists:poshub_shops,id'],
        'courier_type_id' => ['nullable', 'sometimes', 'exists:poshub_courier_types,code'],
        'table_id' => ['nullable', 'sometimes', 'exists:poshub_tables,id'],
        'cancellation_id' => ['nullable', 'sometimes', 'exists:poshub_cancellation_reasons,code'],
        'cancellation_comment' => ['nullable', 'sometimes', 'string'],
        'delivery_cost' => ['nullable', 'sometimes', 'numeric'],
        'delivery_vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'delivery_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'delivery_remarks' => ['nullable', 'sometimes', 'string'],
        'payment_method_id' => ['nullable', 'sometimes', 'exists:poshub_payment_methods,code'],
        'payment_session_id' => ['nullable', 'sometimes'],
        'is_asap' => ['nullable', 'sometimes', 'boolean'],
        'requested_time' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'order_datetime' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'total_discount' => ['nullable', 'sometimes', 'numeric'],
        'total_price' => ['nullable', 'sometimes', 'numeric'],
        'pays_with' => ['nullable', 'sometimes', 'numeric'],
        'extra_costs' => ['nullable', 'sometimes', 'numeric'],
        'total_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'extra_cost_vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'extra_cost_vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'tip_amount' => ['nullable', 'sometimes', 'numeric'],
        'is_paid' => ['nullable', 'sometimes', 'boolean'],
        'is_pos_sync' => ['nullable', 'boolean'],
        'order_remark' => ['nullable', 'sometimes', 'string'],
        'number_of_guests' => ['nullable', 'sometimes', 'numeric'],
        'course' => ['nullable', 'sometimes', 'string'],
        'fulfillment_issues' => ['nullable', 'sometimes'],
        'distance' => ['nullable', 'sometimes', 'numeric'],
        'invoice_id' => ['nullable', 'sometimes', 'string'],
        'courier_left_at' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'courier_back_at' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'kitchen_id' => ['nullable', 'sometimes', 'exists:poshub_kitchens,id'],
        'courier_id' => ['nullable', 'sometimes'],
        'is_discount_percentage' => ['nullable', 'sometimes', 'boolean'],

        'customer' => ['nullable', 'sometimes'],

        'customer.name' => ['nullable', 'sometimes', 'string'],
        'customer.company_name' => ['nullable', 'sometimes', 'string'],
        'customer.phone_number' => ['nullable', 'sometimes', 'string'],
        'customer.email' => ['nullable', 'sometimes', 'string'],

        'customer.street' => ['nullable', 'sometimes', 'string'],
        'customer.street_extra' => ['nullable', 'sometimes', 'string'],
        'customer.postal_code' => ['nullable', 'sometimes', 'string'],
        'customer.city' => ['nullable', 'sometimes', 'string'],
        'customer.country' => ['nullable', 'sometimes', 'string'],
        'customer.longitude' => ['nullable', 'sometimes', 'string'],
        'customer.latitude' => ['nullable', 'sometimes', 'string'],

        'products' => ['nullable', 'sometimes'],

        'products.*.id' => ['nullable', 'sometimes', 'string'],
        'products.*.name' => ['nullable', 'sometimes', 'string'],
        'products.*.price' => ['nullable', 'sometimes', 'numeric'],
        'products.*.quantity' => ['nullable', 'sometimes', 'numeric'],
        'products.*.remarks' => ['nullable', 'sometimes', 'string'],
        'products.*.is_optional' => ['nullable', 'sometimes', 'boolean'],
        'products.*.vat_id' => ['nullable', 'sometimes', 'exists:poshub_vats,vat_code'],
        'products.*.vat_amount' => ['nullable', 'sometimes', 'numeric'],
        'products.*.discount' => ['nullable', 'sometimes', 'numeric'],
        'products.*.is_discount_percentage' => ['nullable', 'sometimes', 'boolean'],
        'products.*.discounted_amount' => ['nullable', 'sometimes', 'numeric'],

        'discounts' => ['nullable', 'sometimes'],

        'discounts.*.name' => ['nullable', 'sometimes', 'string'],
        'discounts.*.count' => ['nullable', 'sometimes', 'numeric'],
        'discounts.*.discount' => ['nullable', 'sometimes', 'numeric'],

    ];

    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('index', 'show', 'errorOrders', 'orderPull', 'poshubGetAllOrdersPerShop');
        $this->middleware('permission:orders-create')->only('store');
        $this->middleware('permission:orders-update')->only('update');
    }

    /**
     * Key Filling function
     **/

    private function keyChecking(Order $data, $data_details)
    {
        foreach ($data_details as $key => $value) {
            if (array_key_exists($key, $this->createConstrains) || array_key_exists($key, $this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * List of orders
     **/

    public function index()
    {
        return OrderResource::collection(Order::paginate(1000));
    }

    /**
     * Order details
     *
     * @param int $id
     **/

    public function show($id)
    {
        return $this->executeShow($id, Order::class, OrderResource::class);
    }

    /**
     * Order add function
     **/

    public function store(Request $request)
    {
        $data_list = [$request->all()];
        for ($key = 0; $key < count($data_list); $key++) {
            $values = $this->validateRequest($request, $this->createConstrains);

            if (is_null($values)) {
                $res['status'] = 'error';
                $res['error_message'] = $this->errorMessages;
                continue;
            }

            try {
                DB::beginTransaction();
                $customer = Customer::firstOrCreate(
                    [
                        'phone' => $values['customer']['phone_number'] ?? null,
                        'name' => $values['customer']['name'],
                        'email' => $values['customer']['email'] ?? null,
                    ],
                    [
                        'company' => $values['customer']['company_name'] ?? null,
                        'created_by_id' => Auth::id(),
                        'updated_by_id' => Auth::id()
                    ]
                );

                $deliveryAddress = null;
                if (!empty($values['customer']['street']) && !empty($values['customer']['postal_code'])) {
                    $deliveryAddress = Address::firstOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'street' => $values['customer']['street'] . " " . $values['customer']['street_extra'],
                            'street_extra' => $values['customer']['street_extra'] ?? null,
                            'postcode' => $values['customer']['postal_code'],
                            'city' => $values['customer']['city'] ?? null,
                            'country' => $values['customer']['country'] ?? null,
                        ],
                        [
                            'longitude' => $values['customer']['longitude'] ?? null,
                            'latitude' => $values['customer']['latitude'] ?? null,
                            'created_by_id' => Auth::id(),
                            'updated_by_id' => Auth::id()
                        ]
                    );
                }

                $order_source = OrderSourceShop::with('orderSourceType')
                    ->whereHas('orderSourceType', function ($query) use ($values) {
                        $query->where('code', $values['order_source_id']);
                    })
                    ->where('shop_id', $values['shop_id'])
                    ->select('id')
                    ->firstOrFail();

                $values['daily_order_number'] = null;
                if (isset($values['order_number'])) {
                    $dat = explode(' ', $values['order_datetime'], 2)[0];
                    $orders = Order::where('shop_id', '=', $values['shop_id'])
                        ->whereDate('order_datetime', '=', $dat)
                        ->where('is_pos_sync', 1)
                        ->orderBy('order_number', 'asc')
                        ->selectRaw('count(*) as order_no')
                        ->where('order_number', '<=', $values['order_number'])
                        ->first();
                    $values['daily_order_number'] = $orders->order_no + 1;
                }

                $order_status = OrderStatus::where('code', $values['order_status_id'])->firstOrFail();
                if ($order_status->parent_id !== null) {
                    $order_status = OrderStatus::where('id', $order_status->parent_id)->firstOrFail();
                }
                $order_type = OrderType::where('code', $values['order_type_id'])->firstOrFail();
                if ($order_type->parent_id !== null) {
                    $order_type = OrderType::where('id', $order_type->parent_id)->firstOrFail();
                }
                $courier_type = CourierType::where('code', $values['courier_type_id'])->firstOrFail();
                if ($courier_type->parent_id !== null) {
                    $courier_type = CourierType::where('id', $courier_type->parent_id)->firstOrFail();
                }
                $payment_method = PaymentMethod::where('code', $values['payment_method_id'])->firstOrFail();
                if ($payment_method->parent_id !== null) {
                    $payment_method = PaymentMethod::where('id', $payment_method->parent_id)->firstOrFail();
                }
                if ($values['order_source_id'] == 'website') {
                    $src_dt = $values['order_datetime'];
                    $src_tz =  new DateTimeZone('Europe/Amsterdam');
                    $dest_tz = new DateTimeZone('UTC');
                    $dt = new DateTime($src_dt, $src_tz);
                    $dt->setTimeZone($dest_tz);

                    $dest_dt = $dt->format('Y-m-d H:i:s');

                    $values['order_datetime'] = $dest_dt;

                    $src_dt = $values['requested_time'];
                    $src_tz =  new DateTimeZone('Europe/Amsterdam');
                    $dest_tz = new DateTimeZone('UTC');
                    $dt = new DateTime($src_dt, $src_tz);
                    $dt->setTimeZone($dest_tz);

                    $dest_dt = $dt->format('Y-m-d H:i:s');

                    $values['requested_time'] = $dest_dt;

                    $values['is_pos_sync'] = 0;
                }
                $order = Order::create([
                    'order_number' => $values['order_number'] ?? null,
                    'daily_order_number' => $values['daily_order_number'],
                    'order_status_id' => $order_status->id,
                    'order_source_id' => $order_source->id,
                    'order_type_id' => $order_type->id,
                    'shop_id' => $values['shop_id'],
                    'courier_type_id' => $courier_type->id,
                    'address_id' => $deliveryAddress !== null ? $deliveryAddress->id : null,
                    'customer_id' => $customer->id,
                    'table_id' => $values['table_id'] ?? null,
                    'kitchen_id' => $values['kitchen_id'] ?? null,
                    'courier_id' => $values['courier_id'] ?? null,
                    'is_discount_percentage' => $values['is_discount_percentage'] ?? 1,
                    'cancellation_id' => isset($values['cancellation_id']) ? CancellationReason::where('code', $values['cancellation_id'])->first()->id : null,
                    'cancellation_comment' => $values['cancellation_comment'] ?? null,
                    'delivery_cost' => $values['delivery_cost'] ?? 0,
                    'delivery_vat_id' => isset($values['delivery_vat_id']) ? Vat::where('vat_code', $values['delivery_vat_id'])->first()->id : null,
                    'delivery_vat_amount' => $values['delivery_vat_amount'] ?? 0,
                    'delivery_remarks' => $values['delivery_remarks'] ?? null,
                    'payment_method_id' => $payment_method->id,
                    'payment_session_id' => $values['payment_session_id'] ?? null,
                    'is_asap' => $values['is_asap'] ?? 0,
                    'requested_time' => $values['requested_time'] ?? null,
                    'order_datetime' => $values['order_datetime'] ?? null,
                    'total_discount' => $values['total_discount'] ?? 0,
                    'pays_with' => $values['pays_with'] ?? null,
                    'extra_costs' => $values['extra_costs'] ?? 0,
                    'is_printed' => $values['is_printed'] ?? 0,
                    'total_vat_amount' => $values['total_vat_amount'] ?? 0,
                    'extra_cost_vat_id' => isset($values['extra_cost_vat_id']) ? Vat::where('vat_code', $values['extra_cost_vat_id'])->first()->id : null,
                    'extra_cost_vat_amount' => $values['extra_cost_vat_amount'] ?? 0,
                    'tip_price' => $values['tip_amount'] ?? 0,
                    'is_paid' => $values['is_paid'] ?? 0,
                    'order_remark' => $values['order_remark'] ?? null,
                    'number_of_guests' => $values['number_of_guests'] ?? 0,
                    'course' => $values['course'] ?? null,
                    'fulfillment_issues' => isset($values['fulfillment_issues']) ? json_encode($values['fulfillment_issues']) : null,
                    'distance' => $values['distance'] ?? null,
                    'invoice_id' => $values['invoice_id'] ?? null,
                    'courier_left_at' => $values['courier_left_at'] ?? null,
                    'courier_back_at' => $values['courier_back_at'] ?? null,
                    'total_price' => $values['total_price'],
                    'is_pos_sync' => $values['is_pos_sync'] ?? 1,
                    'order_json' => json_encode($values),
                    'customer_json' => json_encode($customer),
                    'address_json' => json_encode($deliveryAddress),
                    'created_by_id' => Auth::id(),
                    'updated_by_id' => Auth::id(),
   					'piggy_stage' => $values['piggy_stage'] ?? 0,
                    'piggy_physical_reward' => $values['piggy_physical_reward'] ?? 0,
                ]);

                if (isset($values['discounts'])) {
                    foreach ($values['discounts'] as $dis) {
                        $discount = Discount::firstOrCreate(
                            [
                                'code' => 'pos_hidden',
                                'name' => 'pos default discount',
                            ],
                            [
                                'value' => 0,
                                'is_active' => 0,
                                'created_by_id' => Auth::id(),
                                'updated_by_id' => Auth::id()
                            ]
                        );
                        $order_discount = new OrderDiscount();
                        $order_discount->discount_id = $discount->id;
                        $order_discount->order_id = $order->id;
                        $order_discount->discount_json = json_encode($dis);
                        $order_discount->save(['timestamps' => false]);
                    }
                }

                $total_vat_amount = 0;
                foreach ($values['products'] as $product) {
                    $our_product = Product::where('article_number', $product['id'])->firstOrFail();
                    /*
                    * =====================================================
                    * #########      Product Calculation        #########
                    * =====================================================
                    */
                    $total_price = (float)$product['price'] * (int)$product['quantity'];
                    $vat_amount = $our_product->vats === null ? 0 : ($total_price / 100) * (float)$our_product->vats->percentage;
                    /*
                    * =====================================================
                    * #########      Order Calculation        #########
                    * =====================================================
                    */
                    $total_vat_amount = $total_vat_amount + $vat_amount;

                    OrderProduct::create([
                        'product_id' => $our_product->id,
                        'vat_id' => $our_product->vat_id,
                        'order_id' => $order->id,
                        'quantity' => $product['quantity'],
                        'unit_price' => (float)$product['price'],
                        'total_price' => $total_price,
                        'vat_amount' => $vat_amount,
                        'is_discount_percentage' => $product['is_discount_percentage'] ?? 1,
                        'discount' => $product['discount'] ?? 0,
                        'discounted_amount' => $product['discounted_amount'] ?? $total_price,
                        'remarks' => $product['remarks'] ?? null,
                        'created_by_id' => Auth::id(),
                        'updated_by_id' => Auth::id()
                    ]);
                }


                DB::commit();
                $res['status'] = 'success';
                $res['success_data'] = $order->id;
            } catch (\Exception $exception) {
                DB::rollBack();
                $res['status'] = 'error';
                $res['error_message'] = $exception->getMessage();
                OrderInError::create([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getMessage(),
                    'order_in_transit' => serialize($values),
                    'created_by_id' => 1,
                    'updated_by_id' => 1,
                ]);
            }
        }

        return response($res);
    }

    /**
     * Order update function
     **/

    public function update(Request $request, $orderId)
    {

        $request->merge([
            'order_id' => $orderId,
        ]);
        $values = $this->validateRequest($request, $this->updateConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        try {
            DB::beginTransaction();
            $order = Order::findOrFail($orderId);


            if (isset($values['customer'])) {
                $customer = Customer::findOrFail($order->customer_id);
                if (isset($values['customer']['name'])) {
                    $customer->name = $values['customer']['name'];
                }
                if (isset($values['customer']['company_name'])) {
                    $customer->company = $values['customer']['company_name'];
                }
                if (isset($values['customer']['phone_number'])) {
                    $customer->phone = $values['customer']['phone_number'];
                }
                if (isset($values['customer']['email'])) {
                    $customer->email = $values['customer']['email'];
                }
                $customer->updated_by_id = Auth::id();
                $customer->save();
                $order->customer_json = json_encode($customer);

                if (!empty($values['customer']['street']) && !empty($values['customer']['postal_code'])) {
                    $deliveryAddress = null;
                    if ($order->address_id != null) {
                        $deliveryAddress = Address::find($order->address_id);
                    }
                    if ($deliveryAddress === null) {
                        $deliveryAddress = new Address();
                        $deliveryAddress->created_by_id = Auth::id();
                    }
                    $deliveryAddress->updated_by_id = Auth::id();
                    if (isset($values['customer']['street'])) {
                        $deliveryAddress->street = $values['customer']['street'];
                    }
                    if (isset($values['customer']['street_extra'])) {
                        $deliveryAddress->street_extra = $values['customer']['street_extra'];
                    }
                    if (isset($values['customer']['postal_code'])) {
                        $deliveryAddress->postcode = $values['customer']['postal_code'];
                    }
                    if (isset($values['customer']['city'])) {
                        $deliveryAddress->city = $values['customer']['city'];
                    }
                    if (isset($values['customer']['country'])) {
                        $deliveryAddress->country = $values['customer']['country'];
                    }
                    if (isset($values['customer']['longitude'])) {
                        $deliveryAddress->longitude = $values['customer']['longitude'];
                    }
                    if (isset($values['customer']['latitude'])) {
                        $deliveryAddress->latitude = $values['customer']['latitude'];
                    }
                    $deliveryAddress->save();
                    $order->address_id = $deliveryAddress->id;
                    $order->address_json = json_encode($deliveryAddress);
                }
                unset($values['customer']);
            }

            if (isset($values['discounts'])) {
                $discount = Discount::firstOrCreate(
                    [
                        'code' => 'pos_hidden',
                        'name' => 'pos default discount',
                    ],
                    [
                        'value' => 0,
                        'is_active' => 0,
                        'created_by_id' => Auth::id(),
                        'updated_by_id' => Auth::id()
                    ]
                );
                $d_deleted_row = OrderDiscount::where('discount_id', $discount->id)->where('order_id', $order->id)->delete();
                foreach ($values['discounts'] as $dis) {
                    $order_discount = new OrderDiscount();
                    $order_discount->discount_id = $discount->id;
                    $order_discount->order_id = $order->id;
                    $order_discount->discount_json = json_encode($dis);
                    $order_discount->save(['timestamps' => false]);
                }
                unset($values['discounts']);
            }
            $total_vat_amount = 0;

            if (!empty($values['products'])) {
                $product_id = [];
                foreach ($values['products'] as $product) {
                    $our_product = Product::where('article_number', $product['id'])->firstOrFail();
                    /*
                    * =====================================================
                    * #########      Product Calculation        #########
                    * =====================================================
                    */
                    $total_price = (float)$product['price'] * (int)$product['quantity'];
                    $vat_amount = $our_product->vats === null ? 0 : ($total_price / 100) * (float)$our_product->vats->percentage;
                    /*
                    * =====================================================
                    * #########      Product Calculation        #########
                    * =====================================================
                    */
                    $total_vat_amount = $total_vat_amount + $vat_amount;

                    $saved_product = OrderProduct::updateOrCreate(
                        [
                            'product_id' => $our_product->id,
                            'order_id' => $order->id,
                        ],
                        [
                            'vat_id' => $our_product->vat_id,
                            'quantity' => $product['quantity'],
                            'unit_price' => (float)$product['price'],
                            'total_price' => $total_price,
                            'vat_amount' => $vat_amount,
                            'is_discount_percentage' => isset($product['is_discount_percentage']) ? $product['is_discount_percentage'] : 1,
                            'discount' => isset($product['discount']) ? $product['discount'] : 0,
                            'discounted_amount' => isset($product['discounted_amount']) ? $product['discounted_amount'] : $total_price,
                            'remarks' => $product['remarks'],
                            'created_by_id' => Auth::id(),
                            'updated_by_id' => Auth::id()
                        ]
                    );

                    $product_id[] = $saved_product->id;
                }
                $p_deleted_row = OrderProduct::where('order_id', $order->id)->whereNotIn('id', $product_id)->delete();
            }
            if (isset($values['products'])) {
                unset($values['products']);
            }
            if (isset($values['is_pos_sync'])) {
                $order->is_pos_sync = $values['is_pos_sync'];
            }

            if ($order->order_number === null && isset($values['order_number'])) {
                $dat = explode(' ', $order->order_datetime, 2)[0];
                $orders = Order::where('shop_id', '=', $order->shop_id)
                    ->whereDate('order_datetime', '=', $dat)
                    ->where('is_pos_sync', 1)
                    ->orderBy('order_number', 'asc')
                    ->selectRaw('count(*) as order_no')
                    ->where('order_number', '<=', $values['order_number'])
                    ->first();
                $order->daily_order_number = $orders->order_no + 1;
            }


            if (isset($values['order_number'])) {
                $order->order_number = $values['order_number'];
                unset($values['order_number']);
            }

            if (isset($values['order_type_id'])) {
                $order_type = OrderType::where('code', $values['order_type_id'])->firstOrFail();
                if ($order_type->parent_id !== null) {
                    $order_type = OrderType::where('id', $order_type->parent_id)->firstOrFail();
                }
                $order->order_type_id = $order_type->id;
                unset($values['order_type_id']);
            }

            if (isset($values['courier_type_id'])) {
                $courier_type = CourierType::where('code', $values['courier_type_id'])->firstOrFail();
                if ($courier_type->parent_id !== null) {
                    $courier_type = CourierType::where('id', $courier_type->parent_id)->firstOrFail();
                }
                $order->courier_type_id = $courier_type->id;
                unset($values['courier_type_id']);
            }
            if (isset($values['cancellation_id'])) {
                $order->cancellation_id = CancellationReason::where('code', $values['cancellation_id'])->firstOrFail()->id;
                unset($values['cancellation_id']);
            }
            if (isset($values['delivery_vat_id'])) {
                $order->delivery_vat_id = Vat::where('code', $values['delivery_vat_id'])->firstOrFail()->id;
                unset($values['delivery_vat_id']);
            }
            if (isset($values['payment_method_id'])) {
                $payment_method = PaymentMethod::where('code', $values['payment_method_id'])->firstOrFail();
                if ($payment_method->parent_id !== null) {
                    $payment_method = PaymentMethod::where('id', $payment_method->parent_id)->firstOrFail();
                }
                $order->payment_method_id = $payment_method->id;
                unset($values['payment_method_id']);
            }
            if (isset($values['extra_cost_vat_id'])) {
                $order->extra_cost_vat_id = Vat::where('code', $values['extra_cost_vat_id'])->firstOrFail()->id;
                unset($values['extra_cost_vat_id']);
            }
            if (isset($values['fulfillment_issues'])) {
                $order->fulfillment_issues = json_encode($values['fulfillment_issues']);
                unset($values['fulfillment_issues']);
            }
            if (isset($values['tip_amount'])) {
                $values['tip_price'] = $values['tip_amount'];
                unset($values['tip_amount']);
            }
            unset($values['order_id']);
            $order = $this->keyChecking($order, $values);

            $order->updated_by_id = Auth::id();
            $order->save();

            DB::commit();
            $res['status'] = 'success';
            $res['success_data'] = new OrderPosCollection($order);;
        } catch (\Exception $exception) {
            DB::rollBack();
            $res['status'] = 'error';
            $res['error_message'] = $exception->getMessage();
        }
        return response($res);
    }

    /**
     * New order List as per shop
     *
     * @param int $shopId
     **/

    public function poshubGetNewOrdersCountPerShop($shopId)
    {
        try {
            $validator = Validator::make(
                ['shopId' => $shopId],
                ['shopId' => 'required|exists:poshub_shops,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $new = OrderStatus::where([
            'code' => OrderStatus::NEW,
            'parent_id' => null
        ])->firstOrFail();

        $order = Order::where('shop_id', '=', $shopId)->where('order_status_id', '=', $new->id)->get()->count();

        return $order;
    }


    /**
     * All order List as per shop
     *
     * @param int $shopId
     **/

    public function poshubGetAllOrdersPerShop($shopId)
    {
        $per_page = empty($_GET['per_page']) ? 100 : $_GET['per_page'];
        $sort_by = empty($_GET['sortBy']) ? '' : $_GET['sortBy'];
        $sort_type = empty($_GET['sortByType']) ? '' : $_GET['sortByType'];
        $date = empty($_GET['date']) ? '' : $_GET['date'];
        $status = empty($_GET['status']) ? '' : $_GET['status'];
        $source = empty($_GET['source']) ? '' : $_GET['source'];
        $payment = empty($_GET['payment']) ? '' : $_GET['payment'];
        $type = empty($_GET['type']) ? '' : $_GET['type'];
        $isPos = isset($_GET['isPos']) ? $_GET['isPos'] : '';
        try {
            $validator = Validator::make(
                ['shopId' => $shopId],
                ['shopId' => 'required|exists:poshub_shops,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $order = Order::where('shop_id', '=', $shopId);

        if ($date !== '') {
            $src_dt = $date;

            $src_tz = new DateTimeZone('Europe/Amsterdam');
            $dest_tz =  new DateTimeZone('UTC');
            $dt = new DateTime($src_dt , $src_tz);
            $dt->setTimeZone($dest_tz);

                $dest_dt = $dt->format('Y-m-d');

            $order = $order->where('order_datetime' , "<=" ,  "$date 22:00:00")->where('order_datetime' , ">=" , "$dest_dt 22:00:00");
        }

        if ($status !== '') {
            $order = $order->whereIn('order_status_id', explode(',', $status));
        }

        if ($source !== '') {
            $order = $order->where('order_source_id', $source);
        }

        if ($payment !== '') {
            $order = $order->where('payment_method_id', $payment);
        }

        if ($type !== '') {
            $order = $order->where('order_type_id', $type);
        }

        if ($isPos !== '') {
            $order = $order->where('is_pos_sync', $isPos);
        }

        if ($sort_by !== null && $sort_type !== null) {
            $order = $order->orderBy($sort_by, $sort_type);
        }

        return OrderResource::collection(
            $order->paginate($per_page)
        );
    }

    /**
     * Active order List as per shop
     *
     * @param int $shopId
     **/

    public function poshubGetActiveOrdersPerShop($shopId)
    {
        $per_page = empty($_GET['per_page']) ? 100 : $_GET['per_page'];
        $sort_by = empty($_GET['sortBy']) ? '' : $_GET['sortBy'];
        $sort_type = empty($_GET['sortByType']) ? '' : $_GET['sortByType'];
        $date = empty($_GET['date']) ? '' : $_GET['date'];
        $status = empty($_GET['status']) ? '' : $_GET['status'];
        $source = empty($_GET['source']) ? '' : $_GET['source'];
        $payment = empty($_GET['payment']) ? '' : $_GET['payment'];
        $type = empty($_GET['type']) ? '' : $_GET['type'];
        $isPos = isset($_GET['isPos']) ? $_GET['isPos'] : '';
        try {
            $validator = Validator::make(
                ['shopId' => $shopId],
                ['shopId' => 'required|exists:poshub_shops,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $finished = OrderStatus::where([
            'code' => OrderStatus::FINISHED,
            'parent_id' => null
        ])->firstOrFail();

        $order = Order::where('shop_id', '=', $shopId)
            ->where('order_status_id', '!=', $finished->id);

        if ($date !== '') {
            $order = $order->whereDate('order_datetime', $date);
        }

        if ($status !== '') {
            $order = $order->where('order_status_id', $status);
        }

        if ($source !== '') {
            $order = $order->where('order_source_id', $source);
        }

        if ($payment !== '') {
            $order = $order->where('payment_method_id', $payment);
        }

        if ($type !== '') {
            $order = $order->where('order_type_id', $type);
        }

        if ($isPos !== '') {
            $order = $order->where('is_pos_sync', $isPos);
        }

        if ($sort_by !== null && $sort_type !== null) {
            $order = $order->orderBy($sort_by, $sort_type);
        }

        return OrderResource::collection(
            $order->paginate($per_page)
        );
    }

    /**
     * Error order List
     **/

    public function errorOrders()
    {
        $per_page = empty($_GET['per_page']) ? 100 : $_GET['per_page'];
        $date = empty($_GET['date']) ? null : $_GET['date'];
        $sort_by = empty($_GET['sortBy']) ? null : $_GET['sortBy'];
        $sort_type = empty($_GET['sortByType']) ? null : $_GET['sortByType'];
        $orderInError = new OrderInError();
        if ($date !== null) {
            $orderInError = $orderInError->whereDate('created_at', $date);
        }
        if ($sort_by !== null && $sort_type !== null) {
            $orderInError = $orderInError->orderBy($sort_by, $sort_type);
        }
        return OrderErrorResource::collection($orderInError->paginate($per_page));
    }

    /**
     * Active order list as per shop for API
     *
     * @param int $shopId
     **/


    public function posGetActiveOrdersPerShop($shopId)
    {
        try {
            $validator = Validator::make(
                ['shopId' => $shopId],
                ['shopId' => 'required|exists:poshub_shops,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        return OrderPosCollection::collection(
            Order::where('shop_id', '=', $shopId)
                ->where('is_pos_sync', '=', 0)
                ->orderBy('daily_order_number', 'asc')
                ->get()
        );
    }

    /**
     * Active order List for API
     *
     **/

    public function getActiveOrders()
    {
        return OrderPosCollection::collection(
            Order::where('is_pos_sync', '=', 0)->orderBy('daily_order_number', 'asc')->paginate(1000)
        );
    }


    public function kdsGetDailyOrders(Request $request): JsonResponse
    {
        if ($device_name = $request->get('device_name')) {
            if ($device = Device::query()->where('name', $device_name)->select('shop_id')->first()) {
                $order_type = $request->get('type');
                $order_status = $request->get('status') && in_array($request->get('status'), OrderStatus::KDS_STATUSES)
                    ? explode(',', $request->get('status')) : null;
                $statuses = OrderStatus::KDS_STATUSES;

                $subgroupProductIds = null;
                if ($kitchen_id = $request->get('kitchen')) {
                    if ($kitchen = Kitchen::find($kitchen_id)) {
                        $subgroupIds = count($kitchen->subGroups) ? $kitchen->subGroups()->pluck('subgroup_id') : [];
                        $subgroupProductIds = Product::query()->whereIn('product_subgroup_id', $subgroupIds)->get()->pluck('id');
                    }
                }

                $daily_orders = Order::query()
                    ->where('shop_id', $device->shop_id)
                    ->when($order_type, function ($query) use ($order_type) {
                        $query->whereHas('orderType', function ($q) use ($order_type) {
                            $q->where('code', $order_type);
                        });
                    })
                    ->when($subgroupProductIds, function ($query) use ($subgroupProductIds) {
                        $query->whereHas('orderProducts', function ($q) use ($subgroupProductIds) {
                            $q->whereIn('product_id', $subgroupProductIds);
                        });
                    })
                    ->whereHas('orderStatus', function ($query) use ($statuses, $order_status) {
                        $query->whereIn('code', $order_status ?? $statuses);
                    })
                    ->whereHas('orderProducts')
//                    ->whereRaw("DATE_FORMAT(order_datetime, '%Y-%m-%d') = ?", array(date('Y-m-d')))
                    ->orderBy('order_datetime', 'desc')
                    ->paginate(100);

                $productIds = $daily_orders->map(function ($order) {
                    return $order->orderStatus->code == OrderStatus::KITCHEN
                        ? $order->orderProducts->pluck('product_id')->toArray()
                        : [];
                });

                $productIdsSingleArray = call_user_func_array('array_merge', array(...$productIds));
                $productUniqueIds = array_unique($productIdsSingleArray);
                $productOrderedCounts = array_count_values($productIdsSingleArray);

                $productSubGroups = ProductSubgroup::query()
                    ->with('products')
                    ->whereHas('products', function ($query) use ($productUniqueIds) {
                        $query->whereIn('id', $productUniqueIds);
                    })
                    ->get()
                    ->map(function ($subGroup) use ($productOrderedCounts) {
                        $sub_group = array();
                        $sub_group['name'] = $subGroup->name;
                        $sub_group['products'] = $subGroup->products->map(function($item) use ($productOrderedCounts) {
                            if (array_key_exists($item->id, $productOrderedCounts)) {
                                $product = array();
                                $product['name'] = $item->name;
                                $product['subgroup_id'] = $item->product_subgroup_id;
                                $product['order_count'] = $productOrderedCounts[$item->id];
                                return $product;
                            }
                        });

                        return $sub_group;
                    });

                return response()->json([
                    'orders' => OrderKDSCollection::collection($daily_orders),
                    'settings' => Device::kdsGetDeviceSettingTimes($request),
                    'kitchenProducts' => $productSubGroups
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid Device Name'], 401);
        }

        return response()->json(['success' => false, 'message' => 'Missing Device Name']);
    }


    /**
     * @param $id
     * @return OrderKDSCollection|JsonResponse|mixed
     */
    public function kdsGetOrder(Order $order)
    {
        return $this->executeShow($order->id, Order::class, OrderKDSCollection::class);
    }


    public function kdsUpdateProductStatuses(Request $request, Order $order)
    {
        if ($productIds = $request->get('productIds')) {
            $products = OrderProduct::query()->where('order_id', $order->id);

            if ($products->exists()) {
                $message = 'Products statuses changed.';
                if ($products->count() == count($productIds)) {
                    $finishedStatus = OrderStatus::query()
                        ->where('name', OrderStatus::FINISHED)
                        ->whereNull('parent_id')
                        ->select('id')
                        ->first();

                    $order->update(['order_status_id' => $finishedStatus->id]);
                    $message .= 'and order status set to finished.';
                }

                $products->whereIn('product_id', $productIds);
                if ($products->count() > 0) {
                    $products->update(['status' => 'finished']);
                    return response()->json(['success' => true, 'message' => $message]);
                }

                return response()->json(['success' => false, 'message' => 'There are no products with the requested IDs']);
            }

            return response()->json(['success' => false, 'message' => 'No ordered products with the requested ID']);
        }

        return response()->json(['success' => false, 'message' => 'Product IDs must be sent']);
    }


    public function kdsFinishOrder(Order $order)
    {
        $finishedStatus = OrderStatus::query()
            ->where('name', OrderStatus::FINISHED)
            ->whereNull('parent_id')
            ->select('id')
            ->first();
        $order->update(['order_status_id' => $finishedStatus->id]);
        OrderProduct::where('order_id', $order->id)->update(['status' => 'finished']);

        return response()->json(['success' => true, 'message' => 'Order status changed to finished']);
    }

    public function kdsMoveOrderToKitchen(Order $order)
    {
        $kitchenStatus = OrderStatus::query()
            ->where('name', OrderStatus::KITCHEN)
            ->whereNull('parent_id')
            ->select('id')
            ->first();
        $order->update(['order_status_id' => $kitchenStatus->id]);

        return response()->json(['success' => true, 'message' => 'Order status changed to kitchen']);
    }


    /**
     * New order List for all shop
     *
     **/

    public function poshubGetNewOrders()
    {
        $new = OrderStatus::where([
            'code' => OrderStatus::NEW,
            'parent_id' => null
        ])->firstOrFail();

        return OrderPosCollection::collection(
            Order::where('order_status_id', '=', $new->id)->paginate(1000)
        );
    }

    /**
     * Accepted order List as per shop for API
     *
     * @param int $shopId
     **/

    public function posGetAcceptedOrdersPerShop($shopId)
    {
        try {
            $validator = Validator::make(
                ['shopId' => $shopId],
                ['shopId' => 'required|exists:poshub_shops,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $accepted = OrderStatus::where([
            'code' => OrderStatus::ACCEPTED,
            'parent_id' => null
        ])->firstOrFail();

        return OrderPosCollection::collection(
            Order::where('shop_id', '=', $shopId)
                ->where('order_status_id', '=', $accepted->id)
                ->get()
        );
    }

    /**
     * Set pos_sync to true
     *
     * @param int $orderId
     **/

    public function syncPosOrder(Request $request, $orderId)
    {
        $order_number = $request->get('order_number');
        try {
            $this->currentUser = $request->user();
            $validator = Validator::make(
                ['orderId' => $orderId, 'orderNumber' => $order_number],
                ['orderId' => 'required|uuid|exists:poshub_orders,id', 'orderNumber' => 'required|numeric']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $order = Order::find($orderId);

        $order->is_pos_sync = 1;
        if ($order_number !== null) {
            $order->order_number = $order_number;
        }
        $order->save();
        return new OrderPosCollection($order);
    }

    /**
     * Manual order fetching from active sources
     *
     **/

    public function orderPull()
    {
        $key = empty($_GET['key']) ? false : $_GET['key'];
        if ($key !== 'qazplm@123') {
            return response('API key is not accepted!');
        }
        $order_pull = new OrdersPull();
        $data = $order_pull->handle();
        return response('Done with order' . ': ' . $data);
    }

    /**
     * Manual shop time checking for active sources
     *
     **/

    public function shopsPulse()
    {
        $key = empty($_GET['key']) ? false : $_GET['key'];
        if ($key !== 'qazplm@123') {
            return response('API key is not accepted!');
        }
        $shops_pulse = new ShopsPulse();
        $data = $shops_pulse->handle();
        return response('Done with Shop' . ': ' . $data);
    }
}
