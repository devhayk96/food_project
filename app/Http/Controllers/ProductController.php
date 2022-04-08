<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductSubgroup;
use App\Models\Vat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    /**
     * Store Validation rule preparation
     **/

    protected array $createConstrains = [
        'article_number' => ['nullable', 'string', 'unique:poshub_products,article_number'],
        'vat_id' => ['nullable', 'string'],
        'product_subgroup_id' => ['required', 'exists:poshub_product_subgroups,id'],
        'name' => ['required', 'string'],
        'description_2' => ['nullable', 'string'],
        'description_3' => ['nullable', 'string'],
        'main_product' => ['required', 'boolean'],
        'price' => ['required', 'numeric'],
        'course' => ['nullable', 'numeric'],
        'status' => ['required', 'string'],
        'ean' => ['nullable', 'string'],
        'is_receipt' => ['nullable', 'boolean'],
        'is_kitchen' => ['nullable', 'boolean'],
        'is_sticker' => ['nullable', 'boolean'],
        'is_blocked' => ['required', 'boolean'],
        'is_various' => ['nullable', 'boolean'],
        'is_open_number' => ['nullable', 'boolean'],
        'is_open_price' => ['nullable', 'boolean'],
        'restock' => ['nullable', 'boolean'],
    ];

    /**
     * Update Validation rule preparation
     **/

    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_products,id'],
        'vat_id' => ['nullable', 'string'],
        'product_subgroup_id' => ['nullable', 'exists:poshub_product_subgroups,id'],
        'name' => ['nullable', 'string'],
        'description_2' => ['nullable', 'string'],
        'description_3' => ['nullable', 'string'],
        'main_product' => ['nullable', 'boolean'],
        'price' => ['nullable', 'numeric'],
        'course' => ['nullable', 'numeric'],
        'status' => ['nullable', 'string'],
        'ean' => ['nullable', 'string'],
        'is_receipt' => ['nullable', 'boolean'],
        'is_kitchen' => ['nullable', 'boolean'],
        'is_sticker' => ['nullable', 'boolean'],
        'is_blocked' => ['required', 'boolean'],
        'is_various' => ['nullable', 'boolean'],
        'is_open_number' => ['nullable', 'boolean'],
        'is_open_price' => ['nullable', 'boolean'],
        'restock' => ['nullable', 'boolean'],
    ];

    public function __construct()
    {
        $this->middleware('permission:products-read')->only('index', 'show', 'productExport');
        $this->middleware('permission:products-create')->only('store', 'productImport', 'productExportSample');
        $this->middleware('permission:products-update')->only('update');
    }

    /**
     * Key Filling function
     **/

    private function keyChecking(Product $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * List of products
     **/

    public function index()
    {
        $per_page = empty($_GET['per_page'])?9999:$_GET['per_page'];
        $product_id = empty($_GET['id'])?0:$_GET['id'];
        $name = empty($_GET['name'])?null:$_GET['name'];
        $date = empty($_GET['date'])?null:$_GET['date'];
        $sort_by = empty($_GET['sortBy'])?null:$_GET['sortBy'];
        $sort_type = empty($_GET['sortByType'])?null:$_GET['sortByType'];
        $product_list = new Product();
        if($product_id > 0){
            $product_list = $product_list->where('poshub_products.id', $product_id);
        }
        if($name !== null){
            $product_list = $product_list->where('poshub_products.name','like', '%' . $name . '%' )->orWhere('poshub_products.article_number','like', '%' . $name. '%' );
        }
        if($date !== null){
            $product_list = $product_list->whereDate('created_at', $date);
        }
        if($sort_by !== null && $sort_type !== null){
            $product_list= $product_list->orderBy($sort_by, $sort_type);
        }
        return ProductResource::collection($product_list->paginate($per_page));
    }

    /**
     * Simple List of products without the other joining
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|JsonResponse
     */
    public function simpleProductList(Request $request)
    {
        $per_page = $request->get('per_page') ?? 9999;
        $offset = $request->get('offset') ?? null;
        $product_id = $request->get('id') ?? null;
        $name = $request->get('name') ?? null;
        $date = $request->get('date') ?? null;
        $sort_by = $request->get('sortBy') ?? null;
        $sort_type = $request->get('sortByType') && in_array($request->get('sortByType'), ['asc', 'desc'])
            ? $request->get('sortByType') : 'asc';

        $product_list = Product::query()
            ->when($product_id, function ($query) use ($product_id) {
                $query->where('id', $product_id);
            })
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%')
                    ->orWhere('article_number', 'like', '%' . $name . '%');
            })
            ->when($offset, function ($query) use ($offset) {
                $query->offset($offset);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->when($sort_by, function ($query) use ($sort_by, $sort_type) {
                $query->orderBy($sort_by, $sort_type);
            });

        if ($request->ajax()) {
            return response()->json(
                $product_list->select('id', 'article_number', 'name', 'price', 'image')->limit($per_page)->get()
            );
        }

        return $product_list->paginate($per_page);
    }

    /**
     * Product details
     *
     * @param int $id
     **/

    public function show($id)
    {
        $data = Product::where('article_number',$id)->first();
        if($data === null){
            return response()->json(['errors' => 'Product not Found'], 404);
        }
        return response(new ProductResource($data));
    }

    /**
     * product add function
     **/

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
        $data = Product::create($data_details);
        return response(new ProductResource($data));
    }

       /**
     * Product update function
     **/

    public function update(Request $request, $id)
    {
        $request->merge([
            'id' => $id,
        ]);

        $updateConstrains = array_merge(
            [
                'article_number' => ['nullable', 'string', 'unique:poshub_products,article_number,'.$id],
            ],
            $this->updateConstrains
        );


        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        $data = Product::findOrFail($id);
        $file = $request->file('image');
        if(is_null($request->file('image')) && !is_null($data->image)) {
            $fullpath = $data->image;
            $imag_name = basename($fullpath);
            unlink(public_path('images/uploads/').$imag_name);
            $data->image = null;
        }
        if($file){
            $file_path = date('mdYHis') . uniqid() .'.'. $file->extension();
            $file->move(public_path('images/uploads'), $file_path);
            $data->image = '/images/uploads/'.$file_path;
        }
        $data = $this->keyChecking($data, $data_details);
        $data->updated_at = date_create();
        $data->updated_by = Auth::id();
        $data->save();
        return response(new ProductResource($data));
    }


    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function updateSpecific(Request $request, Product $product): JsonResponse
    {
        try {
            $product->update($request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        try {
            $product->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Export sample product csv to add products
     **/

    public function productExportSample(Request $request)
    {
        $filename = "sample_product_list.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle,
            [
                'article_number',
                'name',
                'description_2',
                'description_3',
                'group',
                'subgroup',
                'vat',
                'price',
                'course',
                'ean',
                'main_product',
                'is_receipt',
                'is_kitchen',
                'is_sticker',
                'is_blocked',
                'is_various',
                'is_open_number',
                'is_open_price',
                'restock',
                'status',
                'created_at',
            ], ",",'"');

        $data['article_number'] = 'EG001';
        $data['name'] = 'Example Product';
        $data['description_2'] = '';
        $data['description_3'] = '';
        $data['group'] = '';
        $data['subgroup'] = 'Example Subgroup';
        $data['vat'] = '';
        $data['price'] = '';
        $data['course'] = '';
        $data['ean'] = '';
        $data['main_product'] = "";
        $data['is_receipt'] = '';
        $data['is_kitchen'] = '';
        $data['is_sticker'] = '';
        $data['is_blocked'] = '';
        $data['is_various'] = '';
        $data['is_open_number'] = '';
        $data['is_open_price'] = '';
        $data['restock'] = '';
        $data['status'] = '';
        $data['created_at'] = '';
        fputcsv($handle, $data, ",",'"');
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return response()->download($filename, 'Sample_Product_List_'.date('Y-m-d').'.csv', $headers)->deleteFileAfterSend(true);
    }

    /**
     * Export all the products as csv file
     **/

    public function productExport(Request $request)
    {
        $filename = "product_list.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle,
            [
                'article_number',
                'name',
                'description_2',
                'description_3',
                'group',
                'subgroup',
                'vat',
                'price',
                'course',
                'ean',
                'main_product',
                'is_receipt',
                'is_kitchen',
                'is_sticker',
                'is_blocked',
                'is_various',
                'is_open_number',
                'is_open_price',
                'restock',
                'status',
                'created_at',
            ], ",",'"');
        $product_list = new Product();

        $product_list = ProductResource::collection($product_list->orderBy('poshub_products.created_at', 'desc')->get());

        foreach($product_list as $key => $rows){
            $data['article_number'] = $rows['article_number'];
            $data['name'] = $rows['name'];
            $data['description_2'] = $rows['description_2'];
            $data['description_3'] = $rows['description_3'];
            $data['group'] = $rows['product_subgroups']['product_groups']['name'];
            $data['subgroup'] = $rows['product_subgroups']['name'];
            $data['vat'] = $rows['vats'] !== null?$rows['vats']['name']: '';
            $data['price'] = $rows['price'];
            $data['course'] = $rows['course'];
            $data['ean'] = $rows['ean'];
            $data['main_product'] = $rows['main_product'];
            $data['is_receipt'] = $rows['is_receipt'];
            $data['is_kitchen'] = $rows['is_kitchen'];
            $data['is_sticker'] = $rows['is_sticker'];
            $data['is_blocked'] = $rows['is_blocked'];
            $data['is_various'] = $rows['is_various'];
            $data['is_open_number'] = $rows['is_open_number'];
            $data['is_open_price'] = $rows['is_open_price'];
            $data['restock'] = $rows['restock'];
            $data['status'] = $rows['status'];
            $data['created_at'] = $rows['created_at'];
            fputcsv($handle, $data, ",",'"');
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return response()->download($filename, 'Product_List_'.date('Y-m-d').'.csv', $headers)->deleteFileAfterSend(true);
    }

    /**
     * Import products for add or update
     **/

    public function productImport(Request $request){
        try {
            $data_list = $this->csvToArray($_FILES['file']['tmp_name']);
            $res['total'] = count($data_list);
            $res['created'] = 0;
            $res['updated'] = 0;
            $res['error'] = 0;
            if(count($data_list) > 0) {
                foreach ($data_list as $key => $data_details) {
                    try {
                        $new = true;
                        $product_subgroup = ProductSubgroup::where('name', 'like', '%' . $data_details['subgroup'] . '%')->first();
                        if ($product_subgroup !== null) {
                            $data = Product::where('article_number', $data_details['article_number'])->first();
                            if ($data) {
                                $new = false;
                                $data->updated_at = date_create();
                                $data->updated_by = Auth::id();
                            } else {
                                $data = new Product();
                                $data->article_number = $data_details['article_number'];
                                $data->created_at = date_create();
                                $data->created_by = Auth::id();
                            }
                            $data->product_subgroup_id = $product_subgroup->id;
                            unset($data_details['group'], $data_details['subgroup'], $data_details['article_number']);
                            if (isset($data_details['vat'])) {
                                $vat_details = Vat::where('name', 'like', '%' . $data_details['vat'] . '%')->first();
                                if ($vat_details !== null) {
                                    $data->vat_id = $vat_details->id;
                                }
                                unset($data_details['vat']);
                            }
                            $data = $this->keyChecking($data, $data_details);
                            $data->save();
                            if ($new) {
                                $res['created'] = $res['created'] + 1;
                            } else {
                                $res['updated'] = $res['updated'] + 1;
                            }
                        } else {
                            $res['error'] = $res['error'] + 1;
                            $data_details['error_message'] = 'Product Subgroup Not Found.';
                            $res['error_data'][] = $data_details;
                        }
                    }
                    catch (\Exception $e) {
                        $res['error'] = $res['error'] + 1;
                        $data_details['error_message'] = $e->getMessage();
                        $res['error_data'][] = $data_details;
                    }
                }
            }
            else{
                $res['error_message'] = "Invalid data, Please check the .csv file";
            }
            return response($res);
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * CSV to Array function
     **/

    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * API function for add or update products
     **/

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
                    $product_subgroup = ProductSubgroup::where('number', $data_details['product_subgroup_id'])->first();
                    if($product_subgroup !== null) {
                        $data = Product::where('article_number', $data_details['article_number'])->first();
                        if($data) {
                            $new = false;
                            $data->updated_at = date_create();
                            $data->updated_by = Auth::id();
                        }
                        else{
                            $data = new Product();
                            $data->article_number = $data_details['article_number'];
                            $data->created_at = date_create();
                            $data->created_by = Auth::id();
                        }
                        $data->product_subgroup_id = $product_subgroup->id;
                        unset($data_details['product_subgroup_id'], $data_details['article_number']);
                        if(isset($data_details['vat_code'])) {
                            $vat_details = Vat::where('vat_code', $data_details['vat_code'])->first();
                            if($vat_details !== null) {
                                $data->vat_id = $vat_details->id;
                            }
                            unset($data_details['vat_code']);
                        }
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
                        $data_list[$key]['error_message'] = 'Product Subgroup Not Found.';
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
