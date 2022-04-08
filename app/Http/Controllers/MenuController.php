<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuCategoryLink;
use App\Models\MenuCategoryProduct;
use App\Models\MenuProduct;
use App\Models\MenuSourceShop;
use App\Models\OrderSourceShop;
use App\Models\OrderSourceType;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\Models\PoshubCategoryProductOptionGroups;

class MenuController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string'],
        'description' => ['nullable', 'string'],
        'start_date' => ['nullable', 'sometimes', 'date_format:Y-m-d'],
        'end_date' => ['nullable', 'sometimes', 'date_format:Y-m-d'],
        'is_blocked' => ['nullable', 'boolean'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_menus,id'],
        'name' => ['nullable', 'string'],
        'description' => ['nullable', 'string'],
        'start_date' => ['nullable', 'sometimes', 'date_format:Y-m-d'],
        'end_date' => ['nullable', 'sometimes', 'date_format:Y-m-d'],
        'is_blocked' => ['nullable', 'boolean'],
    ];

    /**
     * Menu Category Product Price Update
     */

     protected array $updateProductPriceConstrains = [
        'id' => ['required'],
        'price' => ['required','numeric']
     ];

    public function __construct()
    {
        $this->middleware('permission:menus-read')->only('index', 'show');
        $this->middleware('permission:menus-create')->only('store');
        $this->middleware('permission:menus-update')->only('update', 'manageShopSources', 'manageCategories', 'manageProducts', 'manageProductOrder', 'importProductAndGroup');
        $this->middleware('permission:clients-read')->only('getStoreDetails');
        $this->middleware('permission:clients-update')->only('syncStoreMenu', 'setHolidayHours');
    }

    /**
     * Key Filling function
     * @param Menu $data
     * @param $data_details
     * @return Menu
     */
    protected function keyChecking(Menu $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * List of Menu
     **/
    public function index()
    {
        try{
            $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
            $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
            $data_name = empty($_GET['data_name'])?'':$_GET['data_name'];
            $data_list = DB::table('poshub_menus');
            if($data_id > 0){
                $data_list = $data_list->where('poshub_menus.id', $data_id);
            }
            if($data_name !== ''){
                $data_list = $data_list->where('poshub_menus.name',$data_name);
            }
            return $data_list->paginate($per_page);
        }
        catch (\Exception $e) {
            return response($e->getMessage());
        }
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
        $data = Menu::create($data_details);
        return response(new MenuResource($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(new MenuResource(Menu::find($id)));
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
        $data_details = $this->validateRequest($request, $this->updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        try {
            $data = Menu::where('id', $id)->firstOrFail();
            $data = $this->keyChecking($data, $data_details);
            $data->updated_at = date_create();
            $data->updated_by_id = Auth::id();
            $data->save();
            return response(new MenuResource($data));
        }
        catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    /**
     * Update Menu List item status
     *
     * @return \Illiuminate\Https\Response
     *
     */

     public function updateMenuItemStatus( Request $request )
     {
         $data_details = $this->validateRequest($request, $this->updateConstrains);
         if (is_null($data_details)) {
             return response()->json(['errors' => $this->errorMessages], 400);
         }
         try {
             $data = Menu::where('id', $request->menu['id'])->firstOrFail();
             $data->is_blocked = $data->is_blocked === 0 ? 1 : 0;
             $data->save();
             return response()->json(['success' => true], 200);
        }catch(\Excecption $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

     }

     /**
      * Remove Menu
      * @return Illiuminate/Https/Response
      *
      */

      public function deleteMenu( Request $request ) {
          try {
              $data = DB::table('poshub_menus')
              ->where('id', $request->id)->delete();
              return response()->json(['message' => 'Menu Successfully Removed', 'success' => true]);
          }catch(\Excecption $e) {
            return response()->json(['error' => $e->getMessage()], 400);
          }
      }

    /**
     * List of source shop attached to a menu
     **/
    public function sourceShopMenuList()
    {
        try{
            $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
            $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
            $data_name = empty($_GET['data_name'])?'':$_GET['data_name'];
            $shop_id = empty($_GET['shop_id'])?1:$_GET['shop_id'];
            $source_code = empty($_GET['source_code'])?'ubereats':$_GET['source_code'];
            $data_list = DB::table('poshub_menus')
                ->leftJoin('poshub_menu_source_shops', 'poshub_menus.id', 'poshub_menu_source_shops.menu_id')
                ->leftJoin('poshub_order_source_types', 'poshub_menu_source_shops.source_id', 'poshub_order_source_types.id')
                ->where('poshub_menu_source_shops.shop_id', $shop_id)
                ->where('poshub_order_source_types.code', $source_code)
                ->select(
                    'poshub_menus.id',
                    'poshub_menus.name',
                    'poshub_menus.description',
                    'poshub_menus.start_date',
                    'poshub_menus.end_date',
                    'poshub_menus.is_blocked',
                    'poshub_menu_source_shops.is_blocked as is_source_shop_blocked',
                    )
            ;
            if($data_id > 0){
                $data_list = $data_list->where('poshub_menus.id', $data_id);
            }
            if($data_name !== ''){
                $data_list = $data_list->where('poshub_menus.name',$data_name);
            }
            return $data_list->paginate($per_page);
        }
        catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    /**
     * attached the source and shop to the specific menu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageShopSources(Request $request, $id)
    {
        $data_list = $request->all();
        $all_data = MenuSourceShop::where('menu_id', $id)->get();
        DB::beginTransaction();
        try {
            foreach ($all_data as $source_shop) {
                try {
                    $index = false;
                    foreach ($data_list as $key=>$ss) {
                        if ($source_shop->shop_id === $ss['shop_id'] && $source_shop->source_id === $ss['source_id']) {
                            $index = $key;
                            break;
                        }
                    }
                    if ($index === false) {
                        $source_shop->is_blocked = 1;
                    }
                    else{
                        $source_shop->is_blocked = 0;
                        array_splice($data_list, $index, 1);
                    }

                    $source_shop->save();
                    $data[] = $source_shop;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
            foreach ($data_list as $index=>$details) {
                try {
                    $source_shop = new MenuSourceShop();
                    $source_shop->menu_id = $id;
                    $source_shop->shop_id = $details['shop_id'];
                    $source_shop->source_id = $details['source_id'];
                    $source_shop->created_by_id = Auth::id();
                    $source_shop->save();
                    $data[] = $source_shop;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return response($data);
    }

    /**
     * attached the category to the specific menu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageCategories(Request $request, $id)
    {
        $data_list = $request->all();
        $all_menu_category = MenuCategoryLink::where('menu_id', $id)->get();
        DB::beginTransaction();
        try {
            foreach ($all_menu_category as $menu_category) {
                try {
                    $index = array_search($menu_category->menu_category_id, $data_list);
                    if ($index === false) {
                        $menu_category->weight = 0;
                        $menu_category->is_blocked = 1;
                    }
                    else{
                        $menu_category->weight = $index+1;
                        $menu_category->is_blocked = 0;
                    }

                    $menu_category->save();
                    $data[] = $menu_category;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
            foreach ($data_list as $index=>$details) {
                try {
                    if(MenuCategoryLink::where('menu_id', $id)->where('menu_category_id', $details)->first() === null) {
                        $menu_category = new MenuCategoryLink();
                        $menu_category->menu_id = $id;
                        $menu_category->menu_category_id = $details;
                        $menu_category->weight = (int)$index + 1;
                        $menu_category->created_by_id = Auth::id();
                        $menu_category->save();
                        $data[] = $menu_category;
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return response($data);
    }

    /**
     * attached the product to the specific menu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageProducts(Request $request, $id)
    {
        $data_list = $request->all();
        $all_menu_product = MenuProduct::where('menu_id', $id)->get();
        DB::beginTransaction();
        try {
            foreach ($all_menu_product as $menu_product) {
                try {
                    $index = array_search($menu_product->product_id,  array_column($data_list, 'product_id'));
                    if ($index === false) {
                        $menu_product->is_blocked = 1;
                        /*
                        * ============================================================================================
                        * #########      as product is disable so category product need to be disable        #########
                        * ============================================================================================
                        */
                        $all_menu_category_product = MenuCategoryProduct::where('menu_product_id', $menu_product->id)->get();
                        foreach ($all_menu_category_product as $menu_category_product) {
                            try {
                                $menu_category_product->is_blocked = 1;
                                $menu_category_product->save();
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return response($e->getMessage());
                            }
                        }
                    }
                    else{
                        $menu_product->is_blocked = 0;
                        $menu_product->price = $data_list[$index]['price'];
                        /*
                       * ================================================================
                       * #########      searching for menu product category     #########
                       * ================================================================
                       */
                        $all_menu_category_product = MenuCategoryProduct::where('menu_product_id', $menu_product->id)->get();
                        foreach ($all_menu_category_product as $menu_category_product) {
                            try {
                                $index = array_search($menu_category_product->menu_category_id,  $data_list[$index]['categories']);
                                if ($index === false) {
                                    $menu_category_product->is_blocked = 1;
                                }
                                else{
                                    $menu_category_product->is_blocked = 0;
                                    array_splice($data_list[$index]['categories'], $index, 1);
                                }
                                $menu_category_product->save();
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return response($e->getMessage());
                            }
                        }
                        foreach ($data_list[$index]['categories'] as $index => $details) {
                            try {
                                $menu_category_product = new MenuCategoryProduct();
                                $menu_category_product->menu_product_id = $menu_product->id;
                                $menu_category_product->menu_category_id = $details;
                                $menu_category_product->weight = $index + 1;
                                $menu_category_product->price = $menu_product->price;
                                $menu_category_product->created_by_id = Auth::id();
                                $menu_category_product->save();
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return response($e->getMessage());
                            }
                        }

                        array_splice($data_list, $index, 1);
                    }
                    $menu_product->save();
                    $data[] = $menu_product;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
            foreach ($data_list as $index=>$details) {
                try {
                    $menu_product = new MenuProduct();
                    $menu_product->menu_id = $id;
                    $menu_product->product_id = $details['product_id'];
                    $menu_product->price = $details['price'];
                    $menu_product->created_by_id = Auth::id();
                    $menu_product->save();
                    $data[] = $menu_product;

                    foreach ($details['categories'] as $key=>$s_details) {
                        try {
                            $menu_category_product = new MenuCategoryProduct();
                            $menu_category_product->menu_product_id = $menu_product->id;
                            $menu_category_product->menu_category_id = $s_details;
                            $menu_category_product->weight = $key+1;
                            $menu_category_product->created_by_id = Auth::id();
                            $menu_category_product->save();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return response($e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response($e->getMessage());
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return response($data);
    }

    /**
     * update the product weight for menu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function manageProductOrder(Request $request){
        try{
            $data['message'] = 'success';
            $data_list = $request->all();
            DB::beginTransaction();
            foreach ($data_list as $key=>$value){
                $menu_category_product = MenuCategoryProduct::find($value['id']);
                $menu_category_product->weight = $key+1;
                $menu_category_product->save();
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return response($data);
    }

    /**
     * Copy product subgroup to menu category and product to menu product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function importProductAndGroup(Request $request){
        $menu_id = $request->get('menu_id');
        $data['message'] = 'success';
        $group_list = DB::table('poshub_product_groups')
            ->where('poshub_product_groups.is_blocked', '=',0)
            ->leftJoin('poshub_product_subgroups', 'poshub_product_groups.id', '=','poshub_product_subgroups.product_group_id')
            ->where('poshub_product_subgroups.is_blocked', '=',0)
            ->leftJoin('poshub_products', 'poshub_product_subgroups.id', '=','poshub_products.product_subgroup_id')
            ->where('poshub_products.is_blocked', '=',0)
            ->where('poshub_products.status', '=',1)
            ->select(
                'poshub_product_groups.name as group_name',
                'poshub_product_groups.image as group_image',
                'poshub_product_groups.description as group_description',
                'poshub_product_groups.is_drink as group_is_drink',
                'poshub_product_groups.is_food as group_is_food',
                'poshub_product_groups.weight as group_weight',
                'poshub_product_subgroups.name as subgroup_name',
                'poshub_product_subgroups.image as subgroup_image',
                'poshub_product_subgroups.description as subgroup_description',
                'poshub_product_subgroups.weight as subgroup_weight',
                'poshub_products.id as product_id',
                'poshub_products.price as product_price',
                )
            ->get();
        try{
            DB::beginTransaction();
            foreach ($group_list as $key=>$product){
                $menu_product = MenuProduct::where('menu_id', $menu_id)->where('product_id', $product->product_id)->first();
                if($menu_product === null){
                    $menu_product = new MenuProduct();
                    $menu_product->menu_id = $menu_id;
                    $menu_product->product_id = $product->product_id;
                    $menu_product->price = $product->product_price;
                    $menu_product->created_by_id = Auth::id();
                    $menu_product->save();
                }
                $menu_category = MenuCategory::where('name', $product->subgroup_name)->first();
                if($menu_category === null){
                    $menu_category = new MenuCategory();
                    $menu_category->name = $product->subgroup_name;
                    $menu_category->description = $product->subgroup_description;
                    $menu_category->image = $product->subgroup_image;
                    $menu_category->is_food = $product->group_is_food;
                    $menu_category->is_drink = $product->group_is_drink;
                    $menu_category->created_by_id = Auth::id();
                    $menu_category->save();
                }
                $menu_category_link = MenuCategoryLink::where('menu_id', $menu_id)->where('menu_category_id', $menu_category->id)->first();
                if($menu_category_link === null){
                    $menu_category_link = new MenuCategoryLink();
                    $menu_category_link->menu_id = $menu_id;
                    $menu_category_link->menu_category_id = $menu_category->id;
                    $menu_category_link->weight = $product->subgroup_weight;
                    $menu_category_link->created_by_id = Auth::id();
                    $menu_category_link->save();
                }
                $menu_category_product = MenuCategoryProduct::where('menu_product_id', $menu_product->id)->where('menu_category_id', $menu_category->id)->first();
                if($menu_category_product === null){
                    $menu_category_product = new MenuCategoryProduct();
                    $menu_category_product->menu_product_id = $menu_product->id;
                    $menu_category_product->menu_category_id = $menu_category_link->id;
                    $menu_category_product->weight = $key+1;
                    $menu_category_product->created_by_id = Auth::id();
                    $menu_category_product->save();
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }
        DB::commit();
        return response($data);
    }

    /**
     * Get menu list for specific store and source.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStoreMenu(Request $request){
        $data = $request->all();
        $shop_id = $data['shop_id'];
        $source_code = $data['source_code'];
        $shop = Shop::find($shop_id);

        $orderSourceType = OrderSourceType::where('code', $source_code)->first();
        $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
        try {
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
        }
        catch (\Exception $exception){
            return response()->json(
                [
                    'status' => 'ERROR',
                    'offlineReason' => 'CREDENTIAL_MISMATCH',
                    'message' => 'Credential Doesnt match',
                    'menu' => [],
                    'errors' => $exception->getMessage()
                ],
                200
            );
        }
        $status = $client->getStoreMenu($orderSource);

        return response($status);
    }

    /**
     * Syncing the menu to specific source.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function syncStoreMenu(Request $request){
        $data = $request->all();
        $shop_id = $data['shop_id'];
        $source_code = $data['source_code'];
        $menu_list = $data['menu_list'];
        $shop = Shop::find($shop_id);

        $menu['modifier_groups'] = [];
        $menu['display_options']['disable_item_instructions'] = false;
        $menu['items'] = [];
        $menu['categories'] = [];
        foreach ($menu_list as $m_i=>$selected_menu) {
            $menu_id = $selected_menu['id'];
            $menu_details = DB::table('poshub_menus')->where('id', $menu_id)->first();

            $menu['menus'][$m_i]['id'] = 'm-' . (string)$menu_details->id;
            $menu['menus'][$m_i]['title']['translations']['en'] = $menu_details->name;
            $menu['menus'][$m_i]['service_availability'] = $selected_menu['service_availability'];

            $menu_categories = DB::table('poshub_menu_category_links')
                ->join('poshub_menu_categories', 'poshub_menu_category_links.menu_category_id', 'poshub_menu_categories.id')
                ->where('poshub_menu_category_links.menu_id', $menu_id)
                ->where('poshub_menu_category_links.is_blocked', 0)
                ->select('poshub_menu_category_links.id', 'poshub_menu_categories.name')
                ->get();
            foreach ($menu_categories as $key => $menu_category) {
                $category_index = array_search('c-'.$menu_category->id, array_column($menu['categories'], 'id'));

                if($category_index === false) {
                    $menu['categories'][] = [
                        'id' => 'c-' . (string)$menu_category->id,
                        'title' => [
                            'translations' => [
                                'en' => $menu_category->name
                            ]
                        ]
                    ];
                    $category_index = count($menu['categories'])-1;
                }

                $menu['menus'][$m_i]['category_ids'][] = 'c-' . (string)$menu_category->id;

                $menu_category_products = DB::table('poshub_menu_category_products')
                    ->join('poshub_menu_products', 'poshub_menu_category_products.menu_product_id', 'poshub_menu_products.id')
                    ->join('poshub_products', 'poshub_menu_products.product_id', 'poshub_products.id')
                    ->where('poshub_menu_category_products.menu_category_id', $menu_category->id)
                    ->where('poshub_menu_category_products.is_blocked', 0)
                    ->select(
                        'poshub_menu_category_products.id',
                        'poshub_products.name',
                        'poshub_products.description_2 as description',
                        'poshub_products.article_number',
                        'poshub_products.image',
                        'poshub_menu_products.price',
                        'poshub_products.vat_id',
                        )
                    ->get();

                foreach ($menu_category_products as $index => $menu_products) {
                    $item_index = array_search($menu_products->article_number, array_column($menu['items'], 'id'));
                    if ($item_index === false) {
                        $tax = new stdClass();
                        if ($menu_products->vat_id !== null) {
                            $tax ->vat_rate_percentage = (float)DB::table('poshub_vats')
                                ->where('id', $menu_products->vat_id)
                                ->first()
                                ->percentage;
                        }
                        $menu['items'][] = [
                            'id' => $menu_products->article_number,
                            'title' => [
                                'translations' => [
                                    'en' => $menu_products->name
                                ]
                            ],
                            'description' => [
                                'translations' => [
                                    'en' => $menu_products->description
                                ]
                            ],
                            'image_url' => $menu_products->image === null ? null : config('app.url') . $menu_products->image,
                            'price_info' => [
                                'price' => (int)($menu_products->price * 100)
                            ],
                            'tax_info' => $tax,
                        ];
                    }
                    $menu['categories'][$category_index]['entities'][] = [
                        'id' => $menu_products->article_number,
                        'type' => 'ITEM'
                    ];
                }
            }
        }

        $orderSourceType = OrderSourceType::where('code', $source_code)->first();
        $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
        $client = $orderSource->orderSourceType->client_class::make()
            ->initClient($orderSource, $shop);
        $status = $client->setStoreMenu($orderSource, $menu);

        return response($status);
    }


    /**
     * get holiday hours from source for specific shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getHolidayHours(Request $request){
        $data = $request->all();
        $shop_id = $data['shop_id'];
        $source_code = $data['source_code'];
        $shop = Shop::find($shop_id);

        $orderSourceType = OrderSourceType::where('code', $source_code)->first();
        $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
        try {
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
        }
        catch (\Exception $exception){
            return response()->json(
                [
                    'status' => 'ERROR',
                    'offlineReason' => 'CREDENTIAL_MISMATCH',
                    'message' => 'Credential Doesnt match',
                    'menu' => [],
                    'errors' => $exception->getMessage()
                ],
                200
            );
        }
        $status = $client->getHolidayHours($orderSource);

        return response($status);
    }

    /**
     * validate the credentials for source.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkStoreCredentials(Request $request){
        $data = $request->all();
        $shop_id = $data['shop_id'];
        $source_code = $data['source_code'];
        $shop = Shop::find($shop_id);

        $orderSourceType = OrderSourceType::where('code', $source_code)->first();
        $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
        try {
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
        }
        catch (\Exception $exception){
            return response()->json(
                [
                    'status' => false,
                    'offlineReason' => 'CREDENTIAL_MISMATCH',
                    'message' => 'Credential Doesnt match',
                    'errors' => $exception->getMessage()
                ],
                200
            );
        }
        return response(['status' => true]);
    }

    /**
     * get store details from source for specific shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStoreDetails(Request $request){
        $data = $request->all();
        $shop_id = $data['shop_id'];
        $source_code = $data['source_code'];
        $shop = Shop::find($shop_id);

        $orderSourceType = OrderSourceType::where('code', $source_code)->first();
        $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
        try {
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
        }
        catch (\Exception $exception){
            return response()->json(
                [
                    'status' => false,
                    'offlineReason' => 'CREDENTIAL_MISMATCH',
                    'message' => 'Credential Doesnt match',
                    'errors' => $exception->getMessage()
                ],
                200
            );
        }
        return response($client->getStoreDetails($orderSource));
    }

    /**
     * Set or update holiday hours to source for specific shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setHolidayHours(Request $request){
        try {
            $data = $request->all();
            $shop_id = $data['shop_id'];
            $source_code = $data['source_code'];
            $holidayHours['holiday_hours'] = [];
            foreach ($data['holiday_hours'] as $i=>$date) {
                if(isset($date['open_time_periods'])){
                    $holidayHours['holiday_hours'][$date['date']] = [
                        "open_time_periods" => $date['open_time_periods']
                    ];
                }
                else{
                    $holidayHours['holiday_hours'][$date['date']] = [];
                }
            }
            $shop = Shop::find($shop_id);
            $orderSourceType = OrderSourceType::where('code', $source_code)->first();
            $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
            $status = $client->setHolidayHours($orderSource, $holidayHours);
            return response($status);
        }
        catch (\Exception $exception) {
            return response()->json(
                [
                    'errors' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Add Option Groups to Menu Products
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

     public function addOptionGroups(Request $request) {
         $data = $request;
         $menu_id = $data->menuId;
         $addToProducts = $request->addToProducts;
         $optional_group_id = $request->optionGroupId;
         $category_id = $request->category_id;

         if(count($addToProducts) == 1) {
            $option_group = new PoshubCategoryProductOptionGroups();
            $option_group->menu_id = $menu_id;
            $option_group->menu_category_id = $category_id;
            $option_group->product_id = $addToProducts[0]['menu_product']['product_id'];
            $option_group->optional_group_id = $optional_group_id;
            $option_group->save();
            }elseif(count($addToProducts) > 1){
                foreach($addToProducts as $item) {
                    $option_group = new PoshubCategoryProductOptionGroups();
                    $option_group->menu_id = $menu_id;
                    $option_group->menu_category_id = $category_id;
                    $option_group->product_id = $item['menu_product']['product_id'];
                    $option_group->optional_group_id = $optional_group_id;
                    $option_group->save();
                }
            }
     }

     /**
      * Remove Product Optionaol Group
      * @param \Illuminate\Http\Request
      * @return \Illuminate\Http\Response
      */

    public function removeOptionalGroup(Request $request)
    {
        $menu_id = $request->menu_id;
        $product_id = $request->product_id;
        $menu_category_id = $request->menu_category_id;
        try{
            DB::table('poshub_category_product_option_groups')
                ->where('menu_id', $menu_id)
                ->where('product_id', $product_id)
                ->where('menu_category_id', $menu_category_id)
                ->where('optional_group_id', $request->optional_group_id)
                ->delete();
                return response()->json(['message' => 'Optional Group Successfully Removed']);
        }catch(\Exception $exception) {
            return response()->json($exception);
        }
    }


     /**
      * Remove Product from Category
      * @param \Illuminate\Http\Request
      * @return \Illuminate\Http\Response
      */

     public function removeCategoryProduct(Request $response)
     {
         $menu_product_id = $response->menu_product_id;
         $menu_category_id = $response->menu_category_id;
         try{
            DB::table('poshub_menu_category_products')
                ->where('menu_product_id', $menu_product_id)
                ->where('menu_category_id', $menu_category_id)
                ->delete();
                return response()->json(['message' => 'Product Successfully Removed']);
         }catch(\Exception $exception) {
             return response()->json($exception);
         }
     }

     /**
      * Set Category Product Status
      * @param Illuminate\Http\Request
      * @return Illuminate\Http\Response
      */

      public function setCategoryProductStatus(Request $request) {
        $menu_product_id = $request->menu_product_id;
        $menu_category_id = $request->menu_category_id;

        try{
            $data = MenuCategoryProduct::where('menu_product_id', $menu_product_id)
                ->where('menu_category_id', $menu_category_id)->first();
                if($data->is_blocked === 0) {
                    $data->is_blocked = 1;
                }else {
                    $data->is_blocked = 0;
                }
                DB::table('poshub_menu_category_products')
                    ->where('menu_product_id', $menu_product_id)
                    ->where('menu_category_id', $menu_category_id)
                    ->update(['is_blocked' => $data->is_blocked]);
                    return response()->json(['message' => 'Product Status Successfully Updated']);
        }catch(\Exception $exception) {
            return response()->json(['error' => $exception]);
        }
      }

      /**
       * @param Illuminate\Http\Request
       * @return Illuminate\Http\Response
       */
      public function editMenuCategoryProductPrice(Request $request) {
        $update_data = $this->validateRequest($request, $this->updateProductPriceConstrains);
        if(is_null($update_data)) {
            return response()->json(['error' => $this->errorMessages], 400);
        }else {
            try{
                DB::table('poshub_menu_category_products')
                ->where('id', $request->id)
                ->update(['price' => $request->price]);
                return response()->json(['message' => 'Product Price Successfully Updated']);
            }catch(\Exception $exception) {
                return response()->json(['error' => $exception]);
            }
        }
      }

}
