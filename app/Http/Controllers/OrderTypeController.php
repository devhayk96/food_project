<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderTypeResource;
use App\Models\OrderType;
use Illuminate\Http\Request;

class OrderTypeController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|string|min:3|max:64,code',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'is_active' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_order_types,id',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_courier_types,id',
        'is_active' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('index');
        $this->middleware('permission:order_types-read')->only('show');
        $this->middleware('permission:order_types-create')->only('store');
        $this->middleware('permission:order_types-update')->only('update');
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return OrderTypeResource::collection(OrderType::paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $createConstrains = array_merge(
            [
                'parent_id' => ['nullable', 'sometimes', 'exists:poshub_order_types,id',
                    'unique:poshub_order_types,parent_id,NULL,id,code,'.request()->get('code').',source_type_id,'.$request->get('source_type_id'),
                ],
            ],
            $this->createConstrains
        );

        $createMessages = [
            'parent_id.unique' => "An order type with the requested 'code', 'source' and 'parent' already exists"
        ];

        return $this->executeSimpleStore(
            OrderType::class,
            OrderTypeResource::class,
            $request,
            $createConstrains,
            $createMessages
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return $this->executeShow($id, OrderType::class, OrderTypeResource::class);
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
                'code' => ['required', 'string', 'unique:poshub_order_types,code,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(OrderType::class,
            OrderTypeResource::class,
            $request,
            $updateConstrains
        ));
    }
}
