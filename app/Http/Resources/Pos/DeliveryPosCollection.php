<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Request;

class DeliveryPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'courier_type' => $this->courierType->code,
            'executed_time' => $this->executed_time,
            'delivery_costs' => $this->locale->formatPrice($this->delivery_costs),
            'customer_remark' => $this->delivery_remark,
            'address_street' => $this->address->street,
            'address_postcode' => $this->address->postcode,
            'address_city' => $this->address->city,
            'address_extra' => $this->address->extra,
        ];
    }
}
