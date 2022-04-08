<?php

namespace App\Http\Controllers;

use App\Http\Resources\CancellationReasonResource;
use App\Models\CancellationReason;
use Illuminate\Http\Request;

class CancellationReasonController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'nullable|string|min:3|max:64|unique:poshub_cancellation_reasons,code',
        'description' => 'nullable|sometimes|string|min:3|max:512',
        'is_explanation_required' => 'required|boolean',
        'is_blocked' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_cancellation_reasons,id',
        'description' => 'nullable|sometimes|string|min:3|max:512',
        'is_explanation_required' => 'required|boolean',
        'is_blocked' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:cancellation_reasons-read')->only('index', 'show');
        $this->middleware('permission:cancellation_reasons-create')->only('store');
        $this->middleware('permission:cancellation_reasons-update')->only('update');
    }

    /**
     * Cancellation Reason List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        return CancellationReasonResource::collection(CancellationReason::paginate($per_page));
    }

    /**
     * Cancellation Reason details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, CancellationReason::class, CancellationReasonResource::class);
    }

    /**
     * Cancellation Reason store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        try {
            return response($this->executeSimpleStore(
                CancellationReason::class,
                CancellationReasonResource::class,
                $request,
                $this->createConstrains
            ));
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Cancellation Reason update for a particular ID.
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
                'code' => ['nullable', 'string', 'unique:poshub_cancellation_reasons,code,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(CancellationReason::class,
            CancellationReasonResource::class,
            $request,
            $updateConstrains
        ));
    }
}
