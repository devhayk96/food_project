<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class OrderDiscount extends Model
{
    use HasFactory, HasEditHistory;

    protected $table = 'poshub_order_discount';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'order_id',
        'discount_id',
        'discount_json'
    ];}
