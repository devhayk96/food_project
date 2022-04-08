<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourierTypeResource;
use App\Models\CourierType;
use Illuminate\Http\Request;

class CourierTypeController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|string|min:3|max:64',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_courier_types,id',
        'is_active' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_courier_types,id',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'source_type_id' => 'nullable|sometimes|exists:poshub_order_source_types,id',
        'parent_id' => 'nullable|sometimes|exists:poshub_courier_types,id',
        'is_active' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:courier_types-read')->only('index', 'show');
        $this->middleware('permission:courier_types-create')->only('store');
        $this->middleware('permission:courier_types-update')->only('update');
    }

    /**
     * Courier Type List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return CourierTypeResource::collection(CourierType::paginate(1000));
    }

    /**
     * Courier Type details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, CourierType::class, CourierTypeResource::class);
    }

    /**
     * Courier Type store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        try {
            return $this->executeSimpleStore(
                CourierType::class,
                CourierTypeResource::class,
                $request,
                $this->createConstrains
            );
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Courier Type update for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'id' => $id,
        ]);
        $updateConstrains = array_merge(
            [
                'code' => ['nullable', 'string', 'unique:poshub_courier_types,code,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(CourierType::class,
            CourierTypeResource::class,
            $request,
            $updateConstrains
        ));
    }
}
