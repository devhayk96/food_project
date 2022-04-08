<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderSourceTypeCollection extends EditHistoryResource
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
            'shop_ids' => $this->shopIds,
            'client_class' => $this->client_class,
            'credentials_class' => $this->credentials_class,
            'is_active' => $this->is_active
        ]);
    }
}
