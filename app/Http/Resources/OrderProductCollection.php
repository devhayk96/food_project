<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class OrderProductCollection extends EditHistoryResource
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
            'quantity' => $this->quantity,
            'unit_quantity' => $this->unit_quantity,
            'is_discount_percentage' => $this->is_discount_percentage,
            'discount' => $this->discount,
            'remarks' => $this->remarks,
           /* 'total_price' => $this->locale->formatPriceWithCurrency($this->total_price),
            'unit_price' => $this->locale->formatPriceWithCurrency($this->unit_price),
            'discounted_amount' => $this->locale->formatPriceWithCurrency($this->discounted_amount),*/
            'total_price' => $this->total_price,
            'unit_price' => $this->unit_price,
            'discounted_amount' => $this->discounted_amount,
            'product_details' => new ProductResource($this->productDetails)
        ]);
    }
}
