<?php

namespace App\Models;

trait HasEditHistory
{
    public function createdBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by_id');
    }
}
