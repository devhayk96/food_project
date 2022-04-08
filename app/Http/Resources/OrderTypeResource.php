<?php

namespace App\Http\Resources;

use App\Models\OrderType;
use Illuminate\Http\Request;

class OrderTypeResource extends SourceTypeBasedHierarchicalRelationshipCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $array = parent::toArray($request);
        return $this->decorateWithParent($array, OrderType::class);
    }
}
