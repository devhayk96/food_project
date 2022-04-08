<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;
use DB;

class CustomerController extends Controller
{

    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'name' => 'required|string|min:3|max:256',
        'company' => 'nullable|sometimes|min:3|max:256',
        'phone' => 'required|string|min:4|max:64',
        'email' => 'nullable|sometimes|email:rfc|max:256|unique:poshub_customers,email',
        'note' => 'nullable|sometimes|string|max:1024'
    ];

    /**
     * Customer Type List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return CustomerCollection::collection(Customer::paginate(1000));
    }

    /**
     * Customer details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Customer::class, CustomerResource::class);
    }

    /**
     * Customer store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(Customer::class, CustomerResource::class, $request, $this->createConstrains);
    }

    /**
     * @param Illiuminate/Http/Request $request
     * @return Illiuminate/Http/Response
     */
    function fetchAllCustomers(Request $request) {
        $customers_data = Customer::all();
        return response()->json(['customers_data' => $customers_data], 200);
    }
}
