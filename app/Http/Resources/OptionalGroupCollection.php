<?php

namespace App\Http\Resources;


class OptionalGroupCollection extends EditHistoryResource
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
            'number'        => $this->number,
            'name'          => $this->name,
            'image'         => $this->image,
            'description'   => $this->description,
            'is_active'     => (int)$this->is_active,
            'is_optional'   => (int)$this->is_optional,
            'no_discount'   => (int)$this->no_discount,
            'remarks'       => (int)$this->remarks,
            'type'          => $this->type,
            'type_limit'    => $this->type_limit,
            'products'      => isset($this->products) ? $this->products : null,
//            'productIds'    => isset($this->products) ? $this->products()->pluck('product_id') : null,
        ]);
    }
}
