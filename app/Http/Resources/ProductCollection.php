<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends EditHistoryResource
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
            'article_number' => $this->article_number,
            'product_subgroup_id' => $this->product_subgroup_id,
            'vat_id' => $this->vat_id,
            'name' => $this->name,
            'image' => $this->image,
//            'description_1' => $this->description_1,
            'description_2' => $this->description_2,
            'description_3' => $this->description_3,
            'main_product' => $this->main_product,
            'ean' => $this->ean,
            'is_receipt' => $this->is_receipt,
            'is_kitchen' => $this->is_kitchen,
            'is_sticker' => $this->is_sticker,
            'is_blocked' => $this->is_blocked,
            'is_various' => $this->is_various,
            'is_open_number' => $this->is_open_number,
            'is_open_price' => $this->is_open_price,
            'restock' => $this->restock,
            'course' => $this->course,
            'price' => number_format((float)$this->price, 2, '.', ','),
            'status' => $this->status,
            'vats' => new VatResource($this->vats),
            'product_subgroups' => new ProductSubgroupResource($this->product_subgroups)
        ]);
    }
}
