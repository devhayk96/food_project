<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductSubgroupResource;
use App\Models\ProductSubgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSubgroupController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string'],
        'number' => ['nullable', 'string', 'unique:poshub_product_subgroups,number'],
        'product_group_id' => ['required', 'exists:poshub_product_groups,id'],
        'description' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'weight' => ['nullable', 'numeric'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_product_subgroups,id'],
        'name' => ['nullable', 'string'],
        'product_group_id' => ['nullable', 'exists:poshub_product_groups,id'],
        'description' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'weight' => ['nullable', 'numeric'],
    ];

    public function __construct()
    {
        $this->middleware('permission:product_subgroups-read')->only('index', 'show');
        $this->middleware('permission:product_subgroups-create')->only('store');
        $this->middleware('permission:product_subgroups-update')->only('update');
    }

    /**
     * Key Filling function
     * @param ProductSubgroup $data
     * @param $data_details
     * @return ProductSubgroup
     */
    protected function keyChecking(ProductSubgroup $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * Product Subgroup List.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $per_page = $request->get('per_page') ?? 1000000;
        $data_id = $request->get('data_id');
        $data_name = $request->get('data_name');
        $kitchen_id = $request->get('kitchen_id');

        $data_list = ProductSubgroup::query()
            ->when($data_id, function ($query) use ($data_id) {
                $query->where('id', $data_id);
            })
            ->when($data_name, function ($query) use ($data_name) {
                $query->where('name', $data_name);
            })
            ->when($kitchen_id, function ($query) use ($kitchen_id) {
                $query->whereHas('kitchens', function($query) use($kitchen_id) {
                    $query->where('kitchen_id', $kitchen_id);
                });
            })
            ->orderBy('weight')
            ->paginate($per_page);

        return ProductSubgroupResource::collection($data_list);
    }

    /**
     * Product Subgroup details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        $data = ProductSubgroup::where('number', $id)->first();
        if($data === null){
            return response()->json(['errors' => 'Product Subgroup not Found'], 404);
        }
        return response(new ProductSubgroupResource($data));
    }

    /**
     * Product Subgroup store.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $data_details = $this->validateRequest($request, $this->createConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        $file = $request->file('image');
        if($file){
            $file_path = date('mdYHis') . uniqid() .'.'. $file->extension();
            $file->move(public_path('images/uploads'), $file_path);
            $data_details['image'] = '/images/uploads/'.$file_path;
        }
        $data_details['created_at'] = date_create();
        $data_details['created_by'] = Auth::id();
        $data = ProductSubgroup::create($data_details);
        return response(new ProductSubgroupResource($data));
    }

    /**
     * Product Subgroup update for a particular ID.
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
                'number' => ['nullable', 'string', 'unique:poshub_product_subgroups,number,'.$id],
            ],
            $this->updateConstrains
        );
        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        $data = ProductSubgroup::where('id', $id)->first();
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
        $data->updated_by = Auth::id();
        $data->save();
        return response(new ProductSubgroupResource($data));
    }

    /**
     * API: add or update Product Subgroup.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function apiStore(Request $request)
    {
        try {
            $data_list = $request->all();
            $res['created'] = 0;
            $res['updated'] = 0;
            $res['error'] = 0;
            for($key=0; $key<count($data_list); $key++) {
                try {
                    $new = true;
                    $data_details = $data_list[$key];
                    $product_group = DB::table('poshub_product_groups')->where('number', $data_details['product_group_id'])->first();
                    if($product_group !== null) {
                        $data = ProductSubgroup::where('number', $data_details['id'])->first();
                        if ($data) {
                            $new = false;
                            $data->updated_at = date_create();
                            $data->updated_by = Auth::id();
                        } else {
                            $data = new ProductSubgroup();
                            $data->number = $data_details['id'];
                            $data->created_at = date_create();
                            $data->created_by = Auth::id();
                        }
                        $data->product_group_id = $product_group->id;
                        unset($data_details['id']);
                        unset($data_details['product_group_id']);
                        $data = $this->keyChecking($data, $data_details);
                        $data->save();
                        if($new){
                            $res['created'] = $res['created'] +1;
                        }
                        else{
                            $res['updated'] = $res['updated'] +1;
                        }
                    }
                    else{
                        $res['error'] = $res['error'] +1;
                        $data_list[$key]['error_message'] = 'Product Group Not Found.';
                        $res['error_data'][] = $data_list[$key];
                    }
                }
                catch (\Exception $e) {
                    $res['error'] = $res['error'] +1;
                    $data_list[$key]['error_message'] = $e->getMessage();
                    $res['error_data'][] = $data_list[$key];
                }
            }
            return response($res);
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
