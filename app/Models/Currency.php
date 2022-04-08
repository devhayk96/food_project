<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class Currency extends Model
{
    use HasFactory;
    protected $table = 'poshub_currencies';
    protected $hidden = [];
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'short_name',
        'symbol',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
