<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use Illuminate\Validation\Rule;

class ThuisbezorgdStructuralCheck extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 3;
        $this->stepName = 'Structural validations';
        $this->logChannel = 'thuisbezorgd';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        $this->validateOrder($orderInTransit, [
            'id' => 'required|min:3|max:256',
            'restaurantId' => 'required|string',
            'publicReference' => 'required|string|min:3|max:256',
            'platform' => 'required|min:3|max:256',
            'orderDate' => 'required|date_format:Y-m-d\TH:i:s\Z',
            'orderType' => ['required', 'exists:poshub_order_types,code', Rule::in(['delivery', 'pickup'])],
            'requestedDeliveryTime' => 'nullable|sometimes|string|min:3|max:64',
            'requestedPickupTime' => 'nullable|sometimes|string|min:3|max:64',
            'courier' => 'nullable|sometimes|exists:poshub_courier_types,code',
            'deliveryCosts' => 'nullable|sometimes|numeric',
            'totalPrice' => 'required|numeric',
            'totalDiscount' => 'required|numeric',
            'isPaid' => 'required|boolean',
            'paymentMethod' => 'required|exists:poshub_payment_methods,code',
            'paysWith' => 'nullable|sometimes|numeric',
            'customer' => 'required|array',
            'customer.name' => 'required|string|max:256',
            'customer.companyName' => 'nullable|sometimes|string|max:256',
            'customer.phoneNumber' => 'required|string|min:3|max:64',
            'customer.street' => 'nullable|sometimes|string|max:512',
            'customer.streetNumber' => 'nullable|sometimes|string|max:32',
            'customer.postalCode' => 'nullable|sometimes|string|min:3|max:16',
            'customer.city' => 'nullable|sometimes|string|min:3|max:256',
            'customer.extraAddressInfo' => 'nullable|sometimes|max:1024',
            'products' => 'required|array',
            'products.*.id' => 'required|string|min:1|max:256',
            'products.*.name' => 'required|string|min:1|max:256',
            'products.*.category' => 'required|string|min:1|max:256',
            'products.*.count' => 'required|numeric',
            'products.*.price' => 'required|numeric',
            'products.*.remark' => 'nullable|sometimes|max:1024',
            'products.*.sideDishes' => 'nullable|sometimes|array',
            'products.*.sideDishes.*.id' => 'nullable|sometimes|string|min:1|max:256',
            'products.*.sideDishes.*.name' => 'nullable|sometimes|string|min:3|max:256',
            'products.*.sideDishes.*.count' => 'nullable|sometimes|numeric',
            'products.*.sideDishes.*.price' => 'nullable|sometimes|numeric',
            'discounts' => 'nullable|sometimes|array',
            'discounts.*.name' => 'nullable|sometimes|string|min:1|max:256',
            'discounts.*.count' => 'nullable|sometimes|numeric',
            'discounts.*.price' => 'nullable|sometimes|numeric',
            'remark' => 'nullable|sometimes|max:1024',
            'version' =>  'required|string|min:1|max:256'
        ]);
        return $orderInTransit;
    }
}
