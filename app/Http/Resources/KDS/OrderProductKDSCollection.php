<?php

namespace App\Http\Resources\KDS;

use App\Http\Resources\EditHistoryResource;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderProductKDSCollection extends EditHistoryResource
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
            'id' => $this->productDetails->id,
            'name' => $this->productDetails->name,
            'status' => $this->status,
            'price' => $this->unit_price,
            'quantity' => $this->quantity,
            'remarks' => $this->remarks,
            'checked' => $this->status == 'finished' ? 1 : 0,
            'color' => OrderProduct::statusColor($this->status),
        ];
    }
}
