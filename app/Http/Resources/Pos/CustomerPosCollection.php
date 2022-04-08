<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Request;

class CustomerPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'company' => $this->company,
            'phone_number' => $this->phone,
            'email' => $this->email,
//            'note' => $this->note
        ];
    }
}
