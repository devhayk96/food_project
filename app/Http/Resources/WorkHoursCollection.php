<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class WorkHoursCollection extends EditHistoryResource
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
            'day' => $this->day,
            'type' => $this->type,
            'opening_hour' => $this->opening_hour,
            'closing_hour' => $this->closing_hour,
            'is_open' => $this->is_open,
        ]);
    }
}
