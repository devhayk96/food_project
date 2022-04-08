<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\EditHistoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'street' => isset($this->street)?$this->street: null,
            'street_extra' => isset($this->street_extra)?$this->street_extra: null,
            'postal_code' => isset($this->postcode)?$this->postcode: null,
            'city' => isset($this->city)?$this->city: null,
            'country' => isset($this->country)? $this->country: null,
            'longitude' => isset($this->longitude)?$this->longitude:null,
            'latitude' => isset($this->latitude)?$this->latitude:null,
        ];
    }
}
