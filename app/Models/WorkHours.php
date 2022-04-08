<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class WorkHours
 *
 * opening_hour and closing_hour are in format H:i and considered with locale timezone
 *
 * @package App\Models
 * @mixin Builder
 */
class WorkHours extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    public const TYPE_OPENING = 'opening';

    public const TYPE_DELIVERY = 'delivery';

    protected $table = 'poshub_work_hours';

    protected $fillable = [
        'shop_id',
        'day',
        'type',
        'opening_hour',
        'closing_hour',
        'is_open',
        'created_by_id',
        'updated_by_id'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }
}
