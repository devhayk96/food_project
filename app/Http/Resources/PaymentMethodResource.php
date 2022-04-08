<?php

namespace App\Http\Resources;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodResource extends SourceTypeBasedHierarchicalRelationshipCollection
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
        return $this->decorateWithParent($array, PaymentMethod::class);
    }
}
