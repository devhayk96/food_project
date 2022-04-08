<?php

namespace App\Http\Resources;

use App\Exceptions\LocaleException;
use Illuminate\Http\Request;

class WorkHoursResource extends EditHistoryResource
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
