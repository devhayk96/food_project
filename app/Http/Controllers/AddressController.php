<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'street' => 'required|string|min:3|max:512',
        'street_extra' => 'nullable|sometimes|string|min:1|max:512',
        'postcode' => 'required|string|min:3|max:16',
        'city' => 'nullable|sometimes|string|min:1|max:256',
        'country' => 'nullable|sometimes|string|min:1|max:256'
    ];

    /**
     * Address List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return AddressCollection::collection(Address::paginate(1000));
    }

    /**
     * Address details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Address::class, AddressResource::class);
    }

    /**
     * Address store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(Address::class, AddressResource::class, $request, $this->createConstrains);
    }
}
