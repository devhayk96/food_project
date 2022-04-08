<?php

namespace App\Http\Controllers;

use App\Entities\ThuisbezorgdCredentials;
use App\Entities\UberEatsCredentials;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Address;
use App\Models\OrderSourceShop;
use App\Models\OrderSourceType;
use App\Models\Shop;
use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ShopController extends Controller
{
    protected array $createConstrains = [
        'name' => 'required|string|min:3|max:256',
        'address_street' => 'required|string|min:3|max:512',
        'address_street_extra' => 'nullable|sometimes|string|min:1|max:512',
        'address_postcode' => 'required|string|min:3|max:16',
        'address_city' => 'nullable|sometimes|string|min:1|max:256',
        'address_country' => 'nullable|sometimes|string|min:1|max:256',
        'phone' => 'nullable|sometimes|string|min:1|max:64',
        'email' => 'nullable|sometimes|email|max:256',
        'company_number' => 'nullable|sometimes|string|min:1|max:256',
        'vat' => 'nullable|sometimes|string|min:1|max:256',
        'iban' => 'nullable|sometimes|string|min:1|max:256',
        'delivery_time' => 'nullable|sometimes|number|min:1|max:4096',
        'pickup_time' => 'nullable|sometimes|number|min:1|max:4096',
        'ubereats_restaurant_id' => 'nullable|sometimes|string|min:3|max:256',
        'is_open' => 'required|boolean',
        'is_delivering' => 'required|boolean',
        'is_active' => 'required|boolean',
        'is_visible' => 'required|boolean'
    ];

    protected array $updateConstrains = [
        'name' => 'nullable|sometimes|string|min:3|max:256',
        'address_street' => 'nullable|sometimes|string|min:3|max:512',
        'address_street_extra' => 'nullable|sometimes|string|min:1|max:512',
        'address_postcode' => 'nullable|sometimes|string|min:3|max:16',
        'address_city' => 'nullable|sometimes|string|min:1|max:256',
        'address_country' => 'nullable|sometimes|string|min:1|max:256',
        'phone' => 'nullable|sometimes|string|min:1|max:64',
        'email' => 'nullable|sometimes|email|max:256',
        'company_number' => 'nullable|sometimes|string|min:1|max:256',
        'vat' => 'nullable|sometimes|string|min:1|max:256',
        'iban' => 'nullable|sometimes|string|min:1|max:256',
        'delivery_time' => 'nullable|sometimes|number|min:1|max:4096',
        'pickup_time' => 'nullable|sometimes|number|min:1|max:4096',
        'ubereats_restaurant_id' => 'nullable|sometimes|string|min:3|max:256',
        'is_open' => 'required|boolean',
        'is_delivering' => 'required|boolean',
        'is_active' => 'required|boolean',
        'is_visible' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:shops-read')->only('index', 'show');
        $this->middleware('permission:shops-create')->only('store');
        $this->middleware('permission:shops-update')->only('update', 'updateTime', 'updateAllTime');
        $this->middleware('permission:orders-read')->only('getShopListWhereUser');
    }

    public function index()
    {
        return ShopCollection::collection(Shop::paginate(1000));
    }

    /**
     * @todo: this endpoint should filter only shop visible to that type of user.
     * E.G. if is_visible or is_active & user is admin ...
     *
     * @return AnonymousResourceCollection
     */
    public function getShopListWhereUser()
    {
        $shop_list = ShopCollection::collection(Shop::all());
        return $shop_list;
    }

    public function store(Request $request)
    {
        $values = $this->validateRequest($request, $this->createConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        $addressValues = $this->takeDataWherePrefix($values, 'address_');
        $values['address'] = json_encode($addressValues);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        $values = $this->decorateCreateArrayWithUser($values);
        DB::beginTransaction();
        try {
            $shop = new ShopResource(
                $this->createWithOrWithoutOrderSources($values)
            );
            $source_type_list = OrderSourceType::all();
            for ($i = 0; $i < count($source_type_list); $i++) {
                $this->createBlankSource($shop, $source_type_list[$i]);
            }
            DB::commit();
            return $shop;
        }
        catch (\Exception $e){
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $updateConstrains = array_merge(
            [
                'id' => 'required|exists:poshub_shops,id',
            ],
            $this->updateConstrains
        );
        $values = $this->validateRequest($request, $updateConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        $addressValues = $this->takeDataWherePrefix($values, 'address_');
        $values['address'] = json_encode($addressValues);

        $shop = Shop::findOrFail($values['id']);
        unset($values['id']);
        foreach ($values as $key => $value) {
            $shop->$key = $value;
        }
        $shop->save();

        return new ShopResource($shop);
    }

    public function updateTime(Request $request)
    {
        $constrains = [
            'id' => 'required|exists:poshub_shops,id',
            'time' => 'required|integer|min:5|max:270',
            'type' => 'required|string|in:delivery,pickup'
        ];
        $values = $this->validateRequest($request, $constrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        try {
            $shop = Shop::findOrFail($values['id']);

            if ($values['type'] === 'delivery') {
                $shop->delivery_time = $values['time'];
            } else {
                $shop->pickup_time = $values['time'];
            }

            $shop->updated_by_id = $this->currentUser->id;
            $result = $shop->save();

            return response()->json(['result' => $result], 200);
        } catch (\Throwable $exception) {
            return response()->json(['errors' => $exception->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return $this->executeShow($id, Shop::class, ShopResource::class);
    }

    public function open(Request $request)
    {

    }

    public function openDelivery(Request $request)
    {

    }

    protected function createWithOrWithoutOrderSources(array $values) : Shop
    {
        if (!isset($values['order_sources'])) {
            return Shop::create($values);
        }

        $sources = array_map(
            fn($value) => $value['id'],
            $values['order_sources']
        );
        unset($values['order_sources']);

        $shop = Shop::create($values);
        $shop->orderSources()->attach($sources);

        return $shop;
    }


    private function createBlankSource($shop_details, $source_type_details){
        try {
            switch ($source_type_details['code']) {
                case 'pos':
                    $values['credentials'] = serialize(['token' => null]);
                    break;
                case 'thuisbezorgd':
                    $values['credentials'] = ThuisbezorgdCredentials::initBlank()->serialize();
                    break;
                case 'ubereats' :
                    $values['credentials'] = UberEatsCredentials::initBlank()->serialize();
                    break;
            }
            $order_source_shops = new OrderSourceShop();
            $order_source_shops->order_source_type_id = $source_type_details['id'];
            $order_source_shops->shop_id = $shop_details['id'];
            $order_source_shops->code = $source_type_details['code'] . '-' . $shop_details['id'];
            $order_source_shops->name = $shop_details['name'] . ' ' . $source_type_details['name'];
            $order_source_shops->credentials = $values['credentials'];
            $order_source_shops->created_by_id = Auth::id();
            $order_source_shops->updated_by_id = Auth::id();
            $order_source_shops->created_at = date_create();
            return $order_source_shops->save();
        }
        catch (\Exception $e){
            return $e;
        }
    }

    public function updateAllTime( Request $request){
        try {
            $data = $request->all();
            DB::beginTransaction();
            $status = WorkHours::where('shop_id', $data['shop_id'])->delete();
            $weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            foreach ($data['days'] as $key=>$days) {
//                if ($data['type'] === 'opening') {
                    foreach ($days['timeSlots'] as $main_time) {
                        if(isset($main_time['from']) && isset($main_time['to'])) {
                            $shop_hour = new WorkHours();
                            $shop_hour->shop_id = $data['shop_id'];
                            $shop_hour->day = array_search($key, $weekday);
                            $shop_hour->type = 'opening';
                            $shop_hour->opening_hour = $main_time['from'];
                            $shop_hour->closing_hour = $main_time['to'];
                            $shop_hour->is_open = $days['isOpen'];
                            $shop_hour->created_by_id = Auth::id();
                            $shop_hour->updated_by_id = Auth::id();
                            $shop_hour->updated_at = date_create();
                            $shop_hour->save();
                        }
                    }
                    foreach ($days['delivery'] as $main_time) {
                        if(isset($main_time['from']) && isset($main_time['to'])) {
                            $shop_hour = new WorkHours();
                            $shop_hour->shop_id = $data['shop_id'];
                            $shop_hour->day = array_search($key, $weekday);
                            $shop_hour->type = 'delivery';
                            $shop_hour->opening_hour = $main_time['from'];
                            $shop_hour->closing_hour = $main_time['to'];
                            $shop_hour->is_open = $days['isDelivery'];
                            $shop_hour->created_by_id = Auth::id();
                            $shop_hour->updated_by_id = Auth::id();
                            $shop_hour->updated_at = date_create();
                            $shop_hour->save();
                        }
                    }
            }
            DB::commit();
            return $status;
        }
        catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
