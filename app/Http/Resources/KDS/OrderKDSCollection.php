<?php

namespace App\Http\Resources\KDS;

use App\Http\Resources\EditHistoryResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderKDSCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithId([
            'customer' => $this->customer,
            'daily_order_number' => $this->daily_order_number ?? null,
            'order_number' => $this->order_number ?? null,
            'order_time' => $this->locale->getStringFromSystemToAmPmTimeOnly($this->order_datetime),
            'products' => OrderProductKDSCollection::collection($this->kitchenProducts),
            'productIds' => $this->kitchenProducts()->pluck('product_id'),
            'shop' => $this->shop,
            'source_type' => $this->orderType,
            'status' => $this->orderStatus->code,
            'table_id' => $this->table_id,
            'color' => Order::statusColor($this->orderStatus->code),
        ]);
    }
}
