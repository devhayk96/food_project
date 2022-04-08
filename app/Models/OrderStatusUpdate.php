<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderStatusUpdate
 * @package App\Models
 * @mixin Builder
 */
class OrderStatusUpdate extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_order_status_updates';

//    protected $with = ['order'];
}
