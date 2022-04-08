<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDiscountPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = json_decode($this->discount_json);
        return [
            'name' => $data->name,
            'count' => $data->count,
            'discount' => $data->discount,
        ];
    }
}
