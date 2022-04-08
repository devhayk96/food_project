<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CourierType
 * @package App\Models
 * @mixin Builder
 */
class CourierType extends AbstractSourceTypeBasedHierarchicalRelationship
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_courier_types';

    /**
     * Get the orders for the order status.
     */
    public function deliveries() : HasMany
    {
        return $this->hasMany(
            'App\Models\Delivery',
            'courier_type_id',
            'id'
        );
    }
}
