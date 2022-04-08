<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductGroupResource;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductGroupController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string'],
        'number' => ['nullable', 'string', 'unique:poshub_product_groups,number'],
        'description' => ['nullable', 'string'],
        'kitchen_1_id' => ['nullable', 'string'],
        'kitchen_2_id' => ['nullable', 'string'],
        'kitchen_3_id' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'is_food' => ['nullable', 'boolean'],
        'is_drink' => ['nullable', 'boolean'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_product_groups,id'],
        'name' => ['nullable', 'string'],
        'description' => ['nullable', 'string'],
        'kitchen_1_id' => ['nullable', 'string'],
        'kitchen_2_id' => ['nullable', 'string'],
        'kitchen_3_id' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'is_food' => ['nullable', 'boolean'],
        'is_drink' => ['nullable', 'boolean'],
    ];

    public function __construct()
    {
        $this->middleware('permission:product_groups-read')->only('index', 'show');
        $this->middleware('permission:product_groups-create')->only('store');
        $this->middleware('permission:product_groups-update')->only('update');
    }

    /**
     * Key Filling function
     * @param ProductGroup $data
     * @param $data_details
     * @return ProductGroup
     */
    protected function keyChecking(ProductGroup $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * Product Group List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
        $data_name = empty($_GET['data_name'])?'':$_GET['data_name'];
        $data_list = new ProductGroup();
        if($data_id > 0){
            $data_list->where('poshub_product_groups.id', $data_id);
        }
        if($data_name !== ''){
            $data_list->where('poshub_product_groups.name',$data_name);
        }
        return ProductGroupResource::collection($data_list->orderBy('poshub_product_groups.weight')->paginate($per_page));
    }

    /**
     * Product Group details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        $data = ProductGroup::where('number', $id)->first();
        if($data === null){
            return response()->json(['errors' => 'Product Group not Found'], 404);
        }
        return response(new ProductGroupResource($data));
    }

    /**
     * Product Group store.
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
            $data_details['image'] = 'images/uploads/'.$file_path;
        }
        $data_details['created_at'] = date_create();
        $data_details['created_by'] = Auth::id();
        $data = ProductGroup::create($data_details);
        return response(new ProductGroupResource($data));
    }

    /**
     * Product Group update for a particular ID.
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
                'number' => ['nullable', 'string', 'unique:poshub_product_groups,number,'.$id],
            ],
            $this->updateConstrains
        );
        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        try {
            $data = ProductGroup::where('id', $id)->first();
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
            return response(new ProductGroupResource($data));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * API: add or update Product Group.
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
                    $data = ProductGroup::where('number', $data_details['id'])->first();
                    if ($data) {
                        $new = false;
                        $data->updated_at = date_create();
                        $data->updated_by = Auth::id();
                    }
                    else {
                        $data = new ProductGroup();
                        $data->number = $data_details['id'];
                        $data->created_at = date_create();
                        $data->created_by = Auth::id();
                    }
                    unset($data_details['id']);
                    $data = $this->keyChecking($data, $data_details);
                    $data->save();
                    if($new){
                        $res['created'] = $res['created'] +1;
                    }
                    else{
                        $res['updated'] = $res['updated'] +1;
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
