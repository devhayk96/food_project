<?php

namespace App\Http\Resources;

use App\Exceptions\LocaleException;
use Illuminate\Http\Request;

class GroupResource extends GroupCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     * @throws LocaleException
     */
    public function toArray($request): array
    {
        return $this->decorateWithEditHistory(
            parent::toArray($request)
        );
    }
}
