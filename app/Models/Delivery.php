<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Delivery
 * @package App\Models
 * @mixin Builder
 */
class Delivery extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_deliveries';

    protected $with = ['address', 'courierType'];

    protected $fillable = [
        'courier_type_id',
        'executed_time',
        'delivery_costs',
        'delivery_remark',
        'order_id',
        'address_id',
        'created_by_id',
        'updated_by_id'
    ];

    public function order() : HasOne
    {
        return $this->hasOne('App\Models\Order', 'order_id', 'id');
    }

    public function address() : BelongsTo
    {
        return $this->belongsTo('App\Models\Address', 'address_id', 'id');
    }

    public function courierType() : BelongsTo
    {
        return $this->belongsTo('App\Models\CourierType', 'courier_type_id', 'id');
    }
}
