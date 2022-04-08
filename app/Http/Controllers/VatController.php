<?php

namespace App\Http\Controllers;

use App\Http\Resources\VatResource;
use App\Models\Menu;
use App\Models\Vat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VatController extends Controller
{

    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => ['required', 'string'],
        'vat_code' => ['nullable', 'string', 'unique:poshub_vats,vat_code'],
        'description' => ['nullable', 'string'],
        'percentage' => ['nullable', 'numeric'],
        'is_sales' => ['nullable', 'boolean'],
        'is_purchase' => ['nullable', 'boolean'],
        'is_blocked' => ['nullable', 'boolean'],
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => ['nullable', 'exists:poshub_vats,id'],
        'name' => ['nullable', 'string'],
        'description' => ['nullable', 'string'],
        'percentage' => ['nullable', 'numeric'],
        'is_sales' => ['nullable', 'boolean'],
        'is_purchase' => ['nullable', 'boolean'],
        'is_blocked' => ['nullable', 'boolean'],
    ];

    public function __construct()
    {
        $this->middleware('permission:vats-read')->only('index', 'show');
        $this->middleware('permission:vats-create')->only('store');
        $this->middleware('permission:vats-update')->only('update');
    }

    /**
     * Key Filling function
     * @param Vat $data
     * @param $data_details
     * @return Vat
     */
    protected function keyChecking(Vat $data, $data_details){
        foreach($data_details as $key => $value) {
            if (array_key_exists($key,$this->createConstrains) || array_key_exists($key,$this->updateConstrains))
                $data->$key = $value;
        }
        return $data;
    }

    /**
     * Vat List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        $data_id = empty($_GET['data_id'])?0:$_GET['data_id'];
        $data_code = empty($_GET['data_code'])?'':$_GET['data_code'];
        $data_list = new Vat();
        if($data_id > 0){
            $data_list->where('poshub_vats.id', $data_id);
        }
        if($data_code !== ''){
            $data_list->where('poshub_vats.vat_code',$data_code);
        }
        return VatResource::collection($data_list->paginate($per_page));
    }

    /**
     * VAT details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        $data = Vat::where('vat_code', $id)->first();
        if($data === null){
            return response()->json(['errors' => 'VAT not Found'], 404);
        }
        return response(new VatResource($data));
    }

    /**
     * VAT store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $data_details = $this->validateRequest($request, $this->createConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        $data_details['created_at'] = date_create();
        $data_details['created_by'] = Auth::id();
        $data = Vat::create($data_details);
        return response(new VatResource($data));
    }

    /**
     * VAT update for a particular ID.
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
                'vat_code' => ['nullable', 'string', 'unique:poshub_vats,vat_code,'.$id],
            ],
            $this->updateConstrains
        );
        $data_details = $this->validateRequest($request, $updateConstrains);
        if (is_null($data_details)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }
        try {
            $data = Vat::where('id', $id)->first();
            $data = $this->keyChecking($data, $data_details);
            $data->updated_at = date_create();
            $data->updated_by = Auth::id();
            $data->save();
            return response(new VatResource($data));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * API: add or update VAT.
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
                    $data = Vat::where('vat_code', $data_details['vat_code'])->first();
                    if ($data) {
                        $new = false;
                        $data->updated_at = date_create();
                        $data->updated_by = Auth::id();
                    }
                    else {
                        $data = new Vat();
                        $data->created_at = date_create();
                        $data->created_by = Auth::id();
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
