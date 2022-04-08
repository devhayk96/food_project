<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AddressCollection extends EditHistoryResource
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
            'street' => $this->street,
            'street_extra' => isset($this->street_extra)?$this->street_extra: null,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country' => isset($this->country)? $this->country: null,
            'longitude' => isset($this->longitude)?$this->longitude:null,
            'latitude' => isset($this->latitude)?$this->latitude:null,
        ]);
    }
}
