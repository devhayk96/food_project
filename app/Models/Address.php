<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Address
 * @package App\Models
 * @mixin Builder
 */
class Address extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_addresses';

    protected $fillable = [
        'street',
        'street_extra',
        'postcode',
        'city',
        'country',
        'customer_id',
        'longitude',
        'latitude',
        'type',
        'order_type',
        'created_by_id',
        'updated_by_id'
    ];
}
