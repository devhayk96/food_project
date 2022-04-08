<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    public $timestamps = false;

    public function user()
    {
        $this->hasMany(User::class,'country_id', 'id');
    }
}
