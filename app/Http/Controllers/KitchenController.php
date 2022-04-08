<?php

namespace App\Http\Controllers;

use App\Http\Resources\KitchenResource;
use App\Models\Kitchen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isInstanceOf;

class KitchenController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'number' => 'nullable|string|min:3|max:64|unique:poshub_kitchens,number',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'shop_id' => 'required|exists:poshub_shops,id',
        'is_blocked' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_kitchens,id',
        'number' => 'nullable|string|min:3|max:64',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'shop_id' => 'nullable|sometimes|exists:poshub_shops,id',
        'is_blocked' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:kitchens-read')->only('index', 'show');
        $this->middleware('permission:kitchens-create')->only('store');
        $this->middleware('permission:kitchens-update')->only('update');
    }

    /**
     * Kitchen List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        return KitchenResource::collection(Kitchen::paginate($per_page));
    }

    /**
     * Kitchen details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Kitchen::class, KitchenResource::class);
    }

    /**
     * Kitchen store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $new_kitchen = $this->executeStore(Kitchen::class, $request, $this->createConstrains);

            if ($new_kitchen instanceof Kitchen) {
                DB::commit();
                $this->saveKitchenSubgroups($new_kitchen, $request->get('subGroupList'));
                return response()->json(['success' => true, 'message' => 'Kitchen created successfully', 'kitchen' => $new_kitchen]);
            }

            return $new_kitchen;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Kitchen update for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->merge([
                'id' => $id,
            ]);
            $updateConstrains = array_merge(
                [
                    'number' => ['nullable', 'string', 'unique:poshub_kitchens,number,' . $id],
                ],
                $this->updateConstrains
            );

            $kitchen = $this->executeUpdate(Kitchen::class, $request, $updateConstrains);

            if ($kitchen instanceof Kitchen) {
                DB::commit();
                $this->saveKitchenSubgroups($kitchen, $request->get('subGroupList'));
                return response()->json(['success' => true, 'message' => $kitchen]);
            }

            return $kitchen;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    public function saveKitchenSubgroups($kitchen, $selectedSubGroups)
    {
        $subGroupIds = [];
        foreach ($selectedSubGroups as $subGroup) {
            $subGroupIds[] = $subGroup['id'];
        }

        return $kitchen->subGroups()->sync($subGroupIds);
    }

    public function executeUpdate(string $modelName, Request $request, array $rules)
    {
        $values = $this->validateRequest($request, $rules);

        if (is_null($values)) {
            return ['errors' => $this->errorMessages];
        }

        $model = $modelName::findOrFail($values['id']);
        unset($values['id']);
        foreach ($values as $key => $value) {
            $model->$key = $value;
        }
        $model->updated_by_id = $this->currentUser->id;
        $model->save();
        $model->refresh();

        return $model;
    }

    public function executeStore(string $modelName, Request $request, array $rules, array $messages = [])
    {
        try {
            if (is_null($values = $this->validateRequest($request, $rules, $messages))) {
                return response()->json(['success' => false, 'message' => 'Please check form errors', 'errors' => $this->errorMessages], 422);
            }

            $values = $this->decorateCreateArrayWithUser($values);
            return $modelName::create($values);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 400);
        }
    }
}
