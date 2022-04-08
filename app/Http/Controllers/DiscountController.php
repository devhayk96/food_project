<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountCollection;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|unique:poshub_discounts|min:3|max:32',
        'name' => 'required|min:3|max:256',
        'value' => 'required|numeric',
        'is_active' => 'required|boolean'
    ];

    /**
     * Discount List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return DiscountCollection::collection(Discount::paginate(1000));
    }

    /**
     * Discount details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Discount::class, DiscountResource::class);
    }

    /**
     * Discount store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(Discount::class, DiscountResource::class, $request, $this->createConstrains);
    }

}
