<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DeliveryCollection extends EditHistoryResource
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
            'courier_type' => new SourceTypeBasedHierarchicalRelationshipCollection($this->courierType),
            'executed_time' => $this->executed_time,
            'delivery_costs' => $this->locale->formatPrice($this->delivery_costs),
            'delivery_remark' => $this->delivery_remark,
            'address' => new AddressCollection($this->address)
        ]);
    }
}
