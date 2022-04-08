<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MenuCategoryCollection extends EditHistoryResource
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
            'description' => $this->description,
            'image' => $this->image,
            'is_blocked' => $this->is_blocked,
            'is_food' => $this->is_food,
            'is_drink' => $this->is_drink,
        ]);
    }
}
