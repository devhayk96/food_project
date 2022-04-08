<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;

class UserCollection extends EditHistoryResource
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
            'email' => $this->email,
            'name' => $this->name,
            'verified' => !!$this->email_verified_at,
            'status' => User::getAllStatuses()[$this->status],
            'last_login_ip' => $this->last_login_ip,
            'last_login_at' => $this->last_login_at,
            'created_by' => $this->created_by_id,
            'updated_by' => $this->updated_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
