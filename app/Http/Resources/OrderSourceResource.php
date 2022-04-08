<?php

namespace App\Http\Resources;

use App\Entities\ThuisbezorgdCredentials;
use Illuminate\Http\Request;

class OrderSourceResource extends OrderSourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $array = parent::toArray($request);
        $array['shops'] = ShopCollection::collection([$this->shops]);

        $array['credentials'] = ThuisbezorgdCredentials::make()
            ->initFromSerialized($this->credentials)
            ->toArray();

        return $this->decorateWithEditHistory($array);
    }
}
