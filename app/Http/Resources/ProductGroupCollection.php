<?php

namespace App\Http\Resources;


class ProductGroupCollection extends EditHistoryResource
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
            'kitchen_1_id' => $this->kitchen_1_id,
            'kitchen_2_id' => $this->kitchen_2_id,
            'kitchen_3_id' => $this->kitchen_3_id,
            'is_blocked' => $this->is_blocked,
            'is_food' => $this->is_food,
            'is_drink' => $this->is_drink,
            'weight' => $this->weight
        ]);
    }
}
