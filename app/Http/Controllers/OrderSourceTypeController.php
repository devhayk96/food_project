<?php

namespace App\Http\Controllers;

use App\Entities\FoodyxCredentials;
use App\Http\Resources\OrderSourceTypeCollection;
use App\Http\Resources\OrderSourceTypeResource;
use App\Models\OrderSourceShop;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use App\Models\OrderSourceType;
use Illuminate\Http\Request;
use Validator;

class OrderSourceTypeController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|unique:poshub_order_source_types|min:3|max:32',
        'name' => 'required|string|min:3|max:256',
        'client_class' => 'required|string|min:3|max:512',
        'credentials_class' => 'required|string|min:3|max:512',
        'is_active' => 'required|boolean'
    ];

    /**
     * Order type  create and edit Validation rules
     */
    protected array $createEditRules = [
        'code' => 'required',
        'progress' => 'required',
        'name' => 'required|string',
        'client_class' => 'required',
        'credentials_class' => ['required'],
        'shop_ids' => ['required', 'array'],
        'shop_ids.*' => ['required', 'integer', 'exists:poshub_shops,id'],
    ];

    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('index');
        $this->middleware('permission:order_source_types-read')->only('show');
        $this->middleware('permission:order_source_types-create')->only('store');
        $this->middleware('permission:order_source_types-update')->only('update');
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return OrderSourceTypeCollection::collection(OrderSourceType::paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(
            OrderSourceType::class,
            OrderSourceTypeResource::class,
            $request,
            $this->createConstrains
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->executeShow($id, OrderSourceType::class, OrderSourceTypeResource::class);
    }

    /**
     * @param Request $request
     * @return bool[]|\Illuminate\Http\JsonResponse|void
     */
    public function createOREdit(Request $request)
    {
        $request_data = $request->input();
        $this->createEditRules['credentials_class'] = ['required', 'in:'. implode(',', array_values(OrderSourceType::WEBSHOP_API_CREDENTIALS))];

        DB::beginTransaction();

        if ($request_data['progress'] === "create-source-type") {
            $validate = $this->validateRequest($request, $this->createEditRules);

            if (is_null($validate) ) {
                return response()->json(['errors' => $this->errorMessages], 422);
            }

            try {
                $newSourceType = OrderSourceType::create([
                    'code' => $request_data['code'],
                    'name' => $request_data['name'],
                    'client_class' => OrderSourceType::FOODYX_CREDENTIALS, /* change credential entity by dynamically if needed */
                    'credentials_class' => $request_data['credentials_class'],
                    'is_active' => $request_data['is_active'] == null ? false : $request_data['is_active'],
                    'created_by_id' => $request->user()->id
                ]);

                $webShopCredentials = $this->getWebShopCredentials($request_data['credentials_class']);
                if ($webShopCredentials) {
                    $shops = Shop::whereIn('id', $request->get('shop_ids'))->get();
                    foreach ($shops as $shop) {
                        OrderSourceShop::create([
                            'order_source_type_id' => $newSourceType->id,
                            'shop_id' => $shop->id,
                            'code' => $request_data['name'] . '-' . $shop->id,
                            'name' => $shop->name . ' ' . $request_data['name'],
                            'is_active' => true,
                            'is_auto_accept' => true,
                            'credentials' => $webShopCredentials->serialize(),
                            'created_by_id' => $request->user()->id,
                            'updated_by_id' => $request->user()->id
                        ]);
                    }
                }

                DB::commit();
                return response()->json(['success' => true]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        } else if ( $request_data['progress'] === 'edit-source-type' ) {
            $addToRules = array_merge($this->createEditRules, ['id' => $request->input('id')] );
            $validate = $this->validateRequest( $request, $this->createEditRules );

            if(is_null($validate) ) {
                return response()->json(['errors' => $this->errorMessages], 422);
            }

            try {
                $sourceType = OrderSourceType::where('id', $request_data['id'])->firstOrFail();
                if ($sourceType) {
                    $sourceTypeCredential = $sourceType->credentials_class;

                    $sourceType->update([
                        'code' => $request_data['code'],
                        'name' => $request_data['name'],
                        'client_class' => $request_data['client_class'],
                        'credentials_class' => $sourceTypeCredential == OrderSourceType::THUISBEZORGD_CREDENTIALS ? $request_data['credentials_class'] : $sourceTypeCredential,
                        'is_active' => $request_data['is_active'] == null ? false : $request_data['is_active'],
                    ]);

                    $deletingIds = [];
                    foreach ($sourceType->shop_ids as $shop_id) {
                        if (!in_array($shop_id, $request->get('shop_ids'))) {
                            $deletingIds[] = $shop_id;
                        }
                    }

                    if (count($deletingIds) > 0) {
                        $sourceType->orderSources()
                            ->whereIn('shop_id', $deletingIds)
                            ->where('order_source_type_id', $sourceType->id)
                            ->delete();
                    }

                    $requestedShops = Shop::whereIn('id', $request->get('shop_ids'))->get();
                    $webShopCredentials = $this->getWebShopCredentials($request_data['credentials_class']);
                    if ($webShopCredentials) {
                        foreach ($requestedShops as $shop) {
                            $orderShop = OrderSourceShop::where([
                                'order_source_type_id' => $sourceType->id,
                                'shop_id' => $shop->id,
                            ])->first();

                            if (is_null($orderShop)) {
                                OrderSourceShop::create([
                                    'order_source_type_id' => $sourceType->id,
                                    'shop_id' => $shop->id,
                                    'code' => $request_data['name'] . '-' . $shop->id,
                                    'name' => $shop->name . ' ' . $request_data['name'],
                                    'is_active' => true,
                                    'is_auto_accept' => true,
                                    'credentials' => $webShopCredentials->serialize(),
                                    'created_by_id' => $request->user()->id,
                                    'updated_by_id' => $request->user()->id
                                ]);
                            } else {
                                if ($sourceTypeCredential != $request_data['credentials_class']) {
                                    $orderShop->update([
                                        'credentials' => $webShopCredentials->serialize(),
                                        'created_by_id' => $request->user()->id,
                                        'updated_by_id' => $request->user()->id
                                    ]);
                                }
                            }
                        }
                    }

                    DB::commit();
                    return response()->json(['success' => true]);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function removeType($id)
    {
        $remove = DB::table('poshub_order_source_types')->where('id', $id)->delete();
    }

    /**
     * @param \Illuminate\Http\Request
     */
    public function copyType(Request $request) {
        if( $request->input('id') ) {
            $id = $request->input('id');
            $finded = OrderSourceType::where('id', $id)->first();
            if( $finded )
            {
                $copy = OrderSourceType::create([
                    'code' => $finded['code'],
                    'name' => $finded['name'],
                    'client_class' => $finded['client_class'],
                    'credentials_class' =>$finded['credentials_class'],
                    'is_active' => $finded['is_active'],
                    'created_by_id' => $request->user()->id
                ]);
                if( $copy )
                {
                    return ['success' => true];
                }
            }
        }
    }

    protected function getWebShopCredentials($requestEntity)
    {
        /* check if requested entity is foodyx */
        if ($requestEntity == ($model = OrderSourceType::FOODYX_CREDENTIALS)) {
            return $model::make(['token' => '']);
        }

        return false;
    }
}
