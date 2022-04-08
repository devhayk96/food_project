<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderSourceTypeResource extends OrderSourceTypeCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $array = parent::toArray($request);
        return $this->decorateWithEditHistory($array);
    }
}
