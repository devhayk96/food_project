<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class VatResource extends VatCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithEditHistory(
            parent::toArray($request)
        );
    }
}
