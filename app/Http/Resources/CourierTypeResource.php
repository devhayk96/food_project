<?php

namespace App\Http\Resources;

use App\Models\CourierType;
use Illuminate\Http\Request;

class CourierTypeResource extends SourceTypeBasedHierarchicalRelationshipCollection
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
        return $this->decorateWithParent($array, CourierType::class);
    }
}
