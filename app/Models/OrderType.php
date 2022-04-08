<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderType
 * @package App\Models
 * @mixin Builder
 */
class OrderType extends AbstractSourceTypeBasedHierarchicalRelationship
{
    use HasFactory, HasEditHistory, SoftDeletes;

    public const DELIVERY = 'delivery';

    public const PICKUP = 'pickup';

    protected $table = 'poshub_order_types';

    /**
     * Get the orders for the order status.
     */
    public function orders() : HasMany
    {
        return $this->hasMany(
            'App\Models\Order',
            'order_type_id',
            'id'
        );
    }
}
