<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Request;

class OrderSourceTypePosCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId(['code' => $this->code]);
    }
}
