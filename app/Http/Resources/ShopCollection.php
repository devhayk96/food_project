<?php

namespace App\Http\Resources;

use App\Models\Address;
use Illuminate\Http\Request;

class ShopCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'company_number' => $this->company_number,
            'vat' => $this->vat,
            'iban' => $this->iban,
            'delivery_time' => $this->delivery_time,
            'pickup_time' => $this->pickup_time,
            'address' => json_decode($this->address),
            'order_sources' => $this->orderSources,
            'workHours' => $this->workHours,
            'is_open' => $this->is_open,
            'is_delivering' => $this->is_delivering,
            'is_active' => $this->is_active,
            'is_visible' => $this->is_visible
        ]);
    }
}
