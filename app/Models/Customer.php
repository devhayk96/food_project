<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 * @package App\Models
 * @mixin Builder
 */
class Customer extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_customers';
    protected $with = ['address'];

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'note',
        'created_by_id',
        'updated_by_id'
    ];

    /**
     * Get the orders for the order status.
     */
    public function orders()
    {
        return $this->hasMany(
            'App\Models\Order',
            'customer_id',
            'id'
        );
    }

    public function address() {
        return $this->belongsTo(Address::class, 'id', 'customer_id');
    }
}
