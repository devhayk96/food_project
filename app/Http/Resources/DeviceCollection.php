<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DeviceCollection extends EditHistoryResource
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
            'name' => $this->name,
            'code' => $this->code,
            'shop' => $this->shop,
            'shop_id' => $this->shop_id,
            'orders_auto_refresh_time' => $this->orders_auto_refresh_time,
            'finished_orders_delay_time' => $this->finished_orders_delay_time,
        ]);
    }
}
