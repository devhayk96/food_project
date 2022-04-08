<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|string|min:3|max:64,code',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_payment_methods,id',
        'is_active' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_payment_methods,id',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_courier_types,id',
        'is_active' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('index');
        $this->middleware('permission:payment_methods-read')->only('show');
        $this->middleware('permission:payment_methods-create')->only('store');
        $this->middleware('permission:payment_methods-update')->only('update');
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return PaymentMethodResource::collection(PaymentMethod::paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(
            PaymentMethod::class,
            PaymentMethodResource::class,
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
        return $this->executeShow($id, PaymentMethod::class, PaymentMethodResource::class);
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
                'code' => ['required', 'string', 'unique:poshub_payment_methods,code,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(PaymentMethod::class,
            PaymentMethodResource::class,
            $request,
            $updateConstrains
        ));
    }
}
