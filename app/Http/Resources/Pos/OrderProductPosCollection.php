<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderProductPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->productDetails->article_number,
            'name' => $this->productDetails->name,
//            'details' => $this->productDetails,
            'price' => $this->unit_price,
            'quantity' => $this->quantity,
            'remarks' => $this->remarks,
            'vat_amount' => $this->vat_amount,
            'vat_id' => isset($this->vat)?$this->vat->code:null,
//            'unit_quantity' => $this->unit_quantity,
//            'total_price' => $this->locale->formatPriceWithCurrency($this->total_price),
            'is_discount_percentage' => $this->is_discount_percentage,
            'discount' => $this->discount,
            'discounted_amount' => $this->discounted_amount,
        ];
    }
}
