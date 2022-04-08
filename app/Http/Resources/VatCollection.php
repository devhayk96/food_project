<?php

namespace App\Http\Resources;


class VatCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithId([
            'name' => $this->name,
            'vat_code' => $this->vat_code,
            'description' => $this->description,
            'percentage' => $this->percentage,
            'is_sales' => $this->is_sales,
            'is_purchase' => $this->is_purchase,
            'is_blocked' => $this->is_blocked,
        ]);
    }
}
