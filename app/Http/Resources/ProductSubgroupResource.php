<?php

namespace App\Http\Resources;


class ProductSubgroupResource extends ProductSubgroupCollection
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
