<?php

namespace App\Http\Controllers;

use App\Http\Resources\OptionalGroupResource;
use App\Models\OptionalGroup;
use App\Models\OptionalGroupProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OptionaGroupController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string'],
        'number' => ['nullable', 'string', 'unique:poshub_optional_groups,number'],
        'description' => ['nullable', 'string'],
        'is_active' => ['nullable', 'boolean'],
        'no_discount' => ['nullable', 'boolean'],
        'is_optional' => ['nullable', 'boolean'],
        'remarks' => ['nullable', 'boolean'],
        'type' => ['nullable', 'string'],
        'type_limit' => ['nullable', 'integer'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_optional_groups,id'],
        'name' => ['required', 'string'],
        'description' => ['nullable', 'string'],
        'is_active' => ['nullable', 'boolean'],
        'no_discount' => ['nullable', 'boolean'],
        'is_optional' => ['nullable', 'boolean'],
        'remarks' => ['nullable', 'boolean'],
        'type' => ['nullable', 'string'],
        'type_limit' => ['nullable', 'integer'],
    ];

    public function __construct()
    {
        $this->middleware('permission:optional_groups-read')->only('index', 'show', 'fetchGroupProduct');
        $this->middleware('permission:optional_groups-create')->only('store', 'updateGroupProduct');
        $this->middleware('permission:optional_groups-update')->only('update');
        $this->middleware('permission:optional_groups-delete')->only('delete');
    }

    /**
     * Key Filling function
     * @param OptionalGroup $data
     * @param $data_details
     * @return OptionalGroup
     */
    protected function keyChecking(OptionalGroup $data, $data_details){
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
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
        $data_name = empty($_GET['data_name'])?'':$_GET['data_name'];
        $data_list = new OptionalGroup();
        if($data_id > 0){
            $data_list->where('poshub_optional_groups.id', $data_id);
        }
        if($data_name !== ''){
            $data_list->where('poshub_optional_groups.name',$data_name);
        }
        return OptionalGroupResource::collection($data_list->paginate($per_page));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(new OptionalGroupResource(OptionalGroup::find($id)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $data_details = $this->validateRequest($request, $this->createConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 422);
        }
        $file = $request->file('image');
        if($file){
            $file_path = date('mdYHis') . uniqid() .'.'. $file->extension();
            $file->move(public_path('images/uploads'), $file_path);
            $data_details['image'] = '/images/uploads/'.$file_path;
        }
        $data_details['created_at'] = date_create();
        $data_details['created_by_id'] = Auth::id();
        $data = OptionalGroup::create($data_details);
        return response(new OptionalGroupResource($data));
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
                'number' => ['required', 'string', 'unique:poshub_optional_groups,number,'.$id],
            ],
            $this->updateConstrains
        );
        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 422);
        }
        try {
            $data = OptionalGroup::where('id', $id)->first();
            $file = $request->file('image');
            if($file){
                $file_path = date('mdYHis') . uniqid() .'.'. $file->extension();
                $file->move(public_path('images/uploads'), $file_path);
                $data->image = '/images/uploads/'.$file_path;
            }
            else{
                $data->image = $request->get('image');
            }
            $data = $this->keyChecking($data, $data_details);
            $data->updated_at = date_create();
            $data->updated_by_id = Auth::id();
            $data->save();
            return response(new OptionalGroupResource($data));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param OptionalGroup $optional_group
     * @return JsonResponse
     */
    public function updateSpecific(Request $request, OptionalGroup $optional_group): JsonResponse
    {
        try {
            $optional_group->update($request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            OptionalGroup::destroy($id);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        return response()->json(['message' => 'Option Group deleted successfully']);
    }

    /**
     * @param Request $request
     * @param OptionalGroup $optional_group
     * @return JsonResponse
     */
    public function deleteProductFromGroup(Request $request, OptionalGroup $optional_group): JsonResponse
    {
        if ($productId = $request->get('productId')) {
            try {
                $optional_group->products()->wherePivot('product_id', $productId)->detach();
                return response()->json(['message' => 'Product successfully deleted from option group']);
            } catch (\Exception $exception) {
                return response()->json(['message' => $exception->getMessage()], 400);
            }
        }

        return response()->json(['message' => 'Please send deleting product ID'], 400);
    }

    /**
     * Display the specified resource children.
     *
     * @param Request $request
     * @param $optional_group
     * @return JsonResponse
     */
    public function fetchGroupWithProducts(Request $request, $optional_group): JsonResponse
    {
        $data = OptionalGroup::where('id', $optional_group)->with(['products' => function ($q) {
            $q->orderBy('drag_order', 'asc');
        }])->first();

        return response()->json($data);
    }

    /**
     * Update the specified resource children in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function updateGroupProduct(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $deletingIds = [];
            $productIds = $data_list = $request->all();
            $optional_group = OptionalGroup::find($id)->load('products');

            $lastOrder = OptionalGroupProduct::query()
                ->where('optional_group_id', $id)
                ->orderBy('drag_order', 'desc')
                ->select('drag_order')
                ->first();

            $lastOrder = $lastOrder ? $lastOrder->drag_order : 0;

            foreach ($optional_group->products as $optional_product) {
                if (!in_array($optional_product->id, $data_list)) {
                    $deletingIds[] = $optional_product->id;
                } else {
                    $data[$optional_product->id] = ['is_active' => 1, 'updated_by_id' => Auth::id()];
                }

                $index = array_search($optional_product->id, $data_list);
                unset($productIds[$index]);
            }

            if (count($deletingIds) > 0) {
                OptionalGroupProduct::where('optional_group_id', $id)
                    ->whereIn('product_id', $deletingIds)
                    ->forceDelete();
            }

            foreach ($productIds as $productId) {
                $data[$productId] = ['is_active' => 1, 'created_by_id' => Auth::id(), 'drag_order' => $lastOrder + 1 ];
            }

            if (count($data) > 0) {
                $optional_group->products()->sync($data, false);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGroupProductOrder(Request $request, $id): JsonResponse
    {
        try {
            if (count($request->all()) > 0) {
                OptionalGroupProduct::where('optional_group_id', $id)->forceDelete();
                OptionalGroupProduct::insert($request->all());
                return response()->json(['success' => true, 'message' => 'Order of option group products was changed']);
            }
        } catch(\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        return response()->json(['success' => false, 'message' => 'Request parameters are empty']);
    }

    public function updateSingleProduct(Request $request, OptionalGroup $optional_group)
    {
        DB::beginTransaction();
        try {
            // TODO: attach or detach product to option group

        } catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * @return JsonResponse
     */
    public function getTypesWithLimitsForSelect(): JsonResponse
    {
        $data['types'] = OptionalGroup::getTypesForSelect();
        $data['typeLimits'] = OptionalGroup::getTypesLimitsForSelect();
        return response()->json($data);
    }
}
