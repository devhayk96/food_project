<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerCollection extends EditHistoryResource
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
            'name' => $this->name,
            'company' => $this->company,
            'phone' => $this->phone,
            'email' => $this->email,
            'note' => $this->note
        ]);
    }
}
