<?php

namespace App\Http\Resources;


class CancellationReasonCollection extends EditHistoryResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId([
            'code' => $this->code,
            'description' => $this->description,
            'is_explanation_required' => $this->is_explanation_required,
            'is_blocked' => $this->is_blocked,
        ]);
    }
}
