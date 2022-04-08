<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class Vat extends Model
{
    use HasFactory;

    protected $table = 'poshub_vats';
    protected $hidden = [
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'vat_code',
        'description',
        'percentage',
        'is_sales',
        'is_purchase',
        'is_blocked',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
