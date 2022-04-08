<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Device extends Model
{
    use HasFactory;

    protected $table = 'poshub_devices';

    protected $fillable = ['name', 'code', 'shop_id', 'orders_auto_refresh_time', 'finished_orders_delay_time'];

    const ORDERS_AUTO_REFRESH_DEFAULT_TIME = 30000;

    const FINISHED_ORDERS_DELAY_DEFAULT_TIME = 60000;

    const ORDERS_AUTO_REFRESH_TIMES = [
        5000,
        10000,
        15000,
        20000,
        25000,
        30000,
        35000,
        40000,
        45000,
        50000,
        55000,
        60000,
    ];

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


    /**
     * @param Request $request
     * @return array
     */
    public static function kdsGetDeviceSettingTimes(Request $request): array
    {
        if ($device_name = $request->get('device_name')) {
            if ($device = Device::query()->where('name', $device_name)->select('orders_auto_refresh_time')->first()) {
                $refresh_time = $device->orders_auto_refresh_time ?? Device::ORDERS_AUTO_REFRESH_DEFAULT_TIME;
                $delay_time = $device->finished_orders_delay_time ?? Device::FINISHED_ORDERS_DELAY_DEFAULT_TIME;
                return ['success' => true, 'refresh_time' => $refresh_time, 'delay_time' => $delay_time];
            }
            return ['success' => false, 'message' => 'Invalid Device Name'];
        }

        return ['success' => false, 'message' => 'Missing Device Name'];
    }

}
