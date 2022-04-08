<?php

namespace App\Http\Resources;


class TableCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId([
            'shop_id' => $this->shop_id,
            'shop' => new ShopCollection($this->shop),
            'number' => $this->number,
            'name' => $this->name,
            'is_blocked' => $this->is_blocked,
        ]);
    }
}
