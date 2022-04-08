<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderInError
 * @package App\Models
 * @mixin Builder
 */
class OrderInError extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_orders_in_error';

    protected $fillable = [
        'message',
        'errors',
        'order_in_transit',
        'force',
        'created_by_id',
        'updated_by_id'
    ];
}
