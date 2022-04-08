<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderSourceCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId([
            'order_source_type' => new OrderSourceTypeCollection($this->orderSourceType),
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'is_auto_accept' => $this->is_auto_accept
        ]);
    }
}
