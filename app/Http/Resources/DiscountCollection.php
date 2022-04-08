<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class DiscountCollection extends EditHistoryResource
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
            'code' => $this->code,
            'name' => $this->name,
            'value' => $this->value,
            'is_percentage' => $this->is_percentage,
            'is_employee_discount' => $this->is_employee_discount,
            'is_own_use' => $this->is_own_use,
            'is_active' => $this->is_active
        ]);
    }
}
