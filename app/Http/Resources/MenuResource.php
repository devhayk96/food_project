<?php

namespace App\Http\Resources;


class MenuResource extends MenuCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithEditHistory(
            parent::toArray($request)
        );
    }
}
