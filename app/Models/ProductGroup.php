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
class ProductGroup extends Model
{
    use HasFactory;
    protected $table = 'poshub_product_groups';
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'created_by_id',
        'updated_by_id',
    ];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'number',
        'name',
        'image',
        'description',
        'kitchen_1_id',
        'kitchen_2_id',
        'kitchen_3_id',
        'is_blocked',
        'is_food',
        'is_drink',
        'weight',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
}
