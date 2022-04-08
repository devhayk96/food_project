<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MenuCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $name = $this->name;
        return $this->decorateWithId([
            'name' => $this->name,
            'description' => $this->description,
            'is_blocked' => $this->is_blocked,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'menu_categories' => isset($this->menuCategories)?$this->menuCategories:null,
            'menu_source_shops' => isset($this->menuSourceShops)?$this->menuSourceShops:null,
            'menu_products' => isset($this->menuProducts)?$this->menuProducts:null,
        ]);
    }
}
