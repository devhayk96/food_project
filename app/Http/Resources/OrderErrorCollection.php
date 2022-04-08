<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderErrorCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithId([
            'message' => $this->message,
            'errors' => $this->errors,
            'order' => unserialize($this->order_in_transit),
            'force' => $this->force,
        ]);
    }
}
