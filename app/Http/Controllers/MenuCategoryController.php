<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\MenuCategoryLink;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MenuCategoryResource;

class MenuCategoryController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string', 'unique:poshub_menu_categories,name'],
        'description' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'is_food' => ['nullable', 'boolean'],
        'is_drink' => ['nullable', 'boolean'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_menu_categories,id'],
        'description' => ['nullable', 'string'],
        'is_blocked' => ['nullable', 'boolean'],
        'is_food' => ['nullable', 'boolean'],
        'is_drink' => ['nullable', 'boolean'],
    ];

    public function __construct()
    {
        $this->middleware('permission:menu_categories-read')->only('index', 'show');
        $this->middleware('permission:menu_categories-create')->only('store');
        $this->middleware('permission:menu_categories-update')->only('update');
    }

    /**
     * Key Filling function
     * @param MenuCategory $data
     * @param $data_details
     * @return MenuCategory
     */
    protected function keyChecking(MenuCategory $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * Menu Category List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
        $data_name = empty($_GET['data_name'])?'':$_GET['data_name'];
        $data_list = new MenuCategory();
        if($data_id > 0){
            $data_list->where('poshub_menu_categories.id', $data_id);
        }
        if($data_name !== ''){
            $data_list->where('poshub_menu_categories.name',$data_name);
        }
        return MenuCategoryResource::collection($data_list->paginate($per_page));
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
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        $file = $request->file('image');
        if($file){
            $file_path = date('mdYHis') . uniqid() .'.'. $file->extension();
            $file->move(public_path('images/uploads'), $file_path);
            $data_details['image'] = 'images/uploads/'.$file_path;
        }
        $data_details['created_at'] = date_create();
        $data_details['created_by_id'] = Auth::id();
        $data = MenuCategory::create($data_details);
        return response(new MenuCategoryResource($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(new MenuCategoryResource(MenuCategory::find($id)));
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
                'name' => ['required', 'string', 'unique:poshub_menu_categories,name,'.$id],
            ],
            $this->updateConstrains
        );
        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        try {
            $data = MenuCategory::where('id', $id)->first();
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
            return response(new MenuCategoryResource($data));
        }
        catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

      /**
     *Update Menu Status
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function setCategoryStatus(Request $request, $id)
    {
        $request->merge([
            'id' => $id,
        ]);
        try {
            $update = MenuCategoryLink::where('id', $id)->first();
            $update->is_blocked = $update->is_blocked === 0 ? 1:0;
            $update->updated_at = date_create();
            $update->updated_by_id = Auth::id();
            $update->save();
        }catch(\Exception $e) {
            return response()->json($e->getMessage());
        }
        return response()->json(["status" => $update->is_blocked]);
    }
}
