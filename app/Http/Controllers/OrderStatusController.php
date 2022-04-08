<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|string|min:3|max:64',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_order_statuses,id',
        'is_active' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_order_statuses,id',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_courier_types,id',
        'is_active' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('index');
        $this->middleware('permission:order_statuses-read')->only('show');
        $this->middleware('permission:order_statuses-create')->only('store');
        $this->middleware('permission:order_statuses-update')->only('update');
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return OrderStatusResource::collection(OrderStatus::paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(
            OrderStatus::class,
            OrderStatusResource::class,
            $request,
            $this->createConstrains
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return $this->executeShow($id, OrderStatus::class, OrderStatusResource::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'id' => $id,
        ]);
        $updateConstrains = array_merge(
            [
                'code' => ['required', 'string', 'unique:poshub_order_statuses,code,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(OrderStatus::class,
            OrderStatusResource::class,
            $request,
            $updateConstrains
        ));
    }
}
