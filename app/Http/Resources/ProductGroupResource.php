<?php

namespace App\Http\Resources;


class ProductGroupResource extends ProductGroupCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithEditHistory(
            parent::toArray($request)
        );
    }
}
