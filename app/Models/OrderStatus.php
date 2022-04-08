<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderStatus
 * @package App\Models
 * @mixin Builder
 *        'RECEIVED',
 *        'ACCEPTED',
 *        'REJECTED',
 *        'KITCHEN',
 *        'DELIVERY',
 *        'DELIVERED'.
 *        'ERROR'
 */
class OrderStatus extends AbstractSourceTypeBasedHierarchicalRelationship
{
    use HasFactory, HasEditHistory, SoftDeletes;

    public const NEW = 'new';

    public const DECLINED = 'declined';

    public const ACCEPTED = 'accepted';

    public const RECEIVED = 'received';

    public const KITCHEN = 'kitchen';

    public const DELIVERY = 'delivery';

    public const FINISHED = 'finished';

    public const CANCELLED = 'cancelled';

    public const ERROR = 'error';

    public const KDS_STATUSES = [
        self::NEW,
        self::ACCEPTED,
        self::KITCHEN
    ];

    protected $table = 'poshub_order_statuses';

    /**
     * Get the orders for the order status.
     */
    public function orders() : HasMany
    {
        return $this->hasMany(
            'App\Models\Order',
            'order_status_id',
            'id'
        );
    }
}
