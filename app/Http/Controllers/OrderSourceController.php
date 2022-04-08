<?php

namespace App\Http\Controllers;

use App\Entities\ThuisbezorgdCredentials;
use App\Http\Resources\OrderSourceCollection;
use App\Http\Resources\OrderSourceResource;
use App\Models\OrderSource;
use App\Models\OrderSourceShop;
use Illuminate\Http\Request;

class OrderSourceController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'order_source_type_id' => 'required|exists:poshub_order_source_types,id',
        'code' => 'required|string|min:3|max:32|unique:poshub_order_source_shops,code',
        'name' => 'required|string|min:3|max:256',
        'is_active' => 'required|boolean',
        'is_auto_accept' => 'required|boolean',
        'credentials' => 'required|array'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_order_source_shops,id',
        'is_active' => 'required|boolean',
        'is_auto_accept' => 'required|boolean',
        'credentials' => 'required|array'
    ];

    public function __construct()
    {
        $this->middleware('permission:order_sources-read')->only('index', 'show');
        $this->middleware('permission:order_sources-create')->only('store');
        $this->middleware('permission:shops-update')->only('update');
    }

    /**
     * Key Filling function
     * @param OrderSourceShop $data
     * @param $data_details
     * @return OrderSourceShop
     */
    protected function keyChecking(OrderSourceShop $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */

    public function index()
    {
        return OrderSourceCollection::collection(OrderSourceShop::paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $values = $this->validateRequest($request, $this->createConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        $values['credentials'] = ThuisbezorgdCredentials::make()
            ->init($values['credentials'])
            ->serialize();

        return $this->createAndReturn(OrderSourceShop::class, OrderSourceResource::class, $values);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request)
    {
        $values = $this->validateRequest($request, $this->updateConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        $source = OrderSourceShop::findOrFail($values['id']);

        $source->is_active = $values['is_active'];

        foreach ($values['credentials'] as $key=>$value){
            if($value === null){
                $values['credentials'][$key] = '';
            }
        }
        $source->is_auto_accept = $values['is_auto_accept'];
        $source->credentials = serialize($values['credentials']);

        $source->save();
        $source->refresh();

        return new OrderSourceResource($source);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        try {
            return $this->executeShow($id, OrderSourceShop::class, OrderSourceResource::class);
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
