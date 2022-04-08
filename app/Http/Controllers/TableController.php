<?php

namespace App\Http\Controllers;

use App\Http\Resources\TableResource;
use App\Models\table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'number' => 'nullable|string|min:3|max:64|unique:poshub_tables,number',
        'name' => 'required|sometimes|string|min:3|max:512',
        'shop_id' => 'nullable|sometimes|exists:poshub_shops,id',
        'is_blocked' => 'required|boolean'
    ];

    /**
     * Update Validation rule preparation
     **/
    protected array $updateConstrains = [
        'id' => 'required|exists:poshub_tables,id',
        'name' => 'nullable|sometimes|string|min:3|max:512',
        'shop_id' => 'nullable|sometimes|exists:poshub_shops,id',
        'is_blocked' => 'required|boolean'
    ];

    public function __construct()
    {
        $this->middleware('permission:tables-read')->only('index', 'show');
        $this->middleware('permission:tables-create')->only('store');
        $this->middleware('permission:tables-update')->only('update');
    }

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        $per_page = empty($_GET['per_page'])?1000000:$_GET['per_page'];
        return TableResource::collection(Table::paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        try {
            return response($this->executeSimpleStore(
                Table::class,
                TableResource::class,
                $request,
                $this->createConstrains
            ));
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return $this->executeShow($id, Table::class, TableResource::class);
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
                'number' => ['nullable', 'string', 'unique:poshub_tables,number,'.$id],
            ],
            $this->updateConstrains
        );
        return response()->json($this->executeSimpleUpdate(Table::class,
            TableResource::class,
            $request,
            $this->updateConstrains
        ));
    }
}
