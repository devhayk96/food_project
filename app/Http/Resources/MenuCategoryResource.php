<?php

namespace App\Http\Resources;


class MenuCategoryResource extends MenuCategoryCollection
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
