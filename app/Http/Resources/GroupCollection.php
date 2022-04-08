<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class GroupCollection extends EditHistoryResource
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
            'name' => $this->name,
            'description' => $this->description
        ]);
    }
}
