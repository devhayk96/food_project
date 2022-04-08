<?php

namespace App\Models;

use App\Locale\PoshubLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class WorkDay
 *
 * Date is in locale timezone, opening_datetime and closing_datetime are system timezone
 *
 * @package App\Models
 * @mixin Builder
 */
class WorkDay extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_work_days';

    protected $fillable = [
        'shop_id',
        'date',
        'shift',
        'orders',
        'is_open',
        'opening_datetime',
        'closing_datetime',
        'created_by_id',
        'updated_by_id'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public static function createNewWorkDay(Shop $shop, User $user, int $shift): WorkDay
    {
        $locale = new PoshubLocale();
        $today = $locale->getCarbonNowLocaleTz();

        return WorkDay::create([
            'shop_id' => $shop->id,
            'date' => $today->format('Y-m-d'),
            'shift' => $shift,
            'orders' => 0,
            'is_open' => true,
            'opening_datetime' => $today->setTimezone($locale->poshubSystemTz)->format($locale->poshubLocaleDtFormat),
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }

    /**
     * @param  Shop    $shop
     * @return WorkDay
     */
    public static function getLastWorkDayPerShop(Shop $shop): WorkDay
    {
        $today = PoshubLocale::make()->getCarbonNowLocaleTz();
        return WorkDay::where(['shop_id' => $shop->id])->whereDate('date', '>=', $today->format('Y-m-d') )->firstOrFail();
    }
}
