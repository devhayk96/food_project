<?php

namespace App\Http\Resources;


class ProductSubgroupCollection extends EditHistoryResource
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
            'number' => $this->number,
            'name' => $this->name,
            'image' => $this->image,
            'description' => $this->description,
            'is_blocked' => $this->is_blocked,
            'weight' => $this->weight,
            'product_group_id' => $this->product_group_id,
            'product_groups' => new ProductGroupResource($this->product_groups)
        ]);
    }
}
