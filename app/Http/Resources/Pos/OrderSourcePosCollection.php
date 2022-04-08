<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Request;

class OrderSourcePosCollection extends EditHistoryResource
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
            'code' => $this->code,
            'order_source_type' => new OrderSourcePosCollection($this->orderSourceType)
        ]);
    }
}
