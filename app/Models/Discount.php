<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Discount
 * @package App\Models
 * @mixin Builder
 */
class Discount extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_discounts';

    protected $fillable = [
        'code',
        'name',
        'value',
        'is_percentage',
        'is_employee_discount',
        'is_own_use',
        'is_active',
        'created_by_id',
        'updated_by_id'
    ];

    public function orders()
    {
        return $this->belongsToMany(
            'App\Models\Orders',
            'poshub_order_discount',
            'discount_id',
            'order_id'
        );
    }
}
