<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderErrorResource extends OrderErrorCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithEditHistory(
            parent::toArray($request)
        );
    }
}
