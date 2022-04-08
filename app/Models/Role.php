<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    const SUPERADMIN = 'superadmin';

    public $guarded = [];

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'created_by_id',
        'updated_by_id'
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_id', 'id');
    }
}
