<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryCollection;
use App\Http\Resources\DeliveryResource;
use App\Models\Address;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'courier_type_id' => 'required|exists:poshub_courier_types,id',
        'executed_time' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i:s'],
        'delivery_costs' => ['nullable', 'sometimes', 'numeric'],
        'note' => ['nullable', 'sometimes', 'max:4096'],
        'address_id' => [
            'exclude_unless:address_create_street,null', 'nullable', 'sometimes', 'exists:poshub_addresses,id'
        ],
        'address_create_street' => ['exclude_unless:address_id,null', 'required', 'max:512'],
        'address_create_postcode' => ['exclude_unless:address_id,null', 'required', 'max:16'],
        'address_create_city' => ['exclude_unless:address_id,null', 'nullable', 'sometimes', 'max:256'],
        'address_create_extra' => ['exclude_unless:address_id,null', 'nullable', 'sometimes', 'max:1024'],
        'order_id' => ['nullable', 'sometimes', 'exists:poshub_orders,id'],
    ];

    /**
     * Delivery List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return DeliveryCollection::collection(Delivery::paginate(1000));
    }

    /**
     * Delivery details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Delivery::class, DeliveryResource::class);
    }

    /**
     * Delivery store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $values = $this->validateRequest($request, $this->createConstrains);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        if (empty($values['address_id'])) {
            $addressValues = $this->takeDataWherePrefix($values, 'address_create_');
            $address = Address::create(
                $this->decorateCreateArrayWithUser($addressValues)
            );
            $values['address_id'] = $address->id;
        }

        $values = $this->decorateCreateArrayWithUser($values);
        return $this->createAndReturn(Delivery::class, DeliveryResource::class, $values);
    }
}
