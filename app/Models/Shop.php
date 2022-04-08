<?php

namespace App\Models;

use App\Exceptions\ModelException;
use App\Locale\PoshubLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Shop
 * @package App\Models
 * @mixin Builder
 */
class Shop extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_shops';

    protected $with = ['orderSources','workHours'];

    protected $fillable = [
        'currency_id',
        'name',
        'phone',
        'email',
        'company_number',
        'vat',
        'iban',
        'delivery_time',
        'pickup_time',
        'address',
//        'ubereats_restaurant_id',
        'is_open',
        'is_delivering',
        'is_active',
        'is_visible',
        'created_by_id',
        'updated_by_id'
    ];

    /**
     * The order sources that belong to the shop.
     */
    public function orderSources(): HasMany
    {
        return $this->hasMany(
            OrderSourceShop::class
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            'shop_id',
            'id'
        );
    }

    public function devices(): HasMany
    {
        return $this->hasMany(
            Device::class,
            'shop_id',
            'id'
        );
    }

    public function workHours(): HasMany
    {
        return $this->hasMany(
            WorkHours::class,
            'shop_id',
            'id'
        );
    }

    /**
     * @param  Shop           $shop
     * @param  User           $user
     * @return bool
     * @throws ModelException
     */
    public function openShop(Shop $shop, User $user): bool
    {
        if ($shop->is_open === true) {
            return false;
        }
        $wDay = WorkDay::where('shop_id', '=', $shop->id)->first();

        if (empty($wDay)) {
            $wDay = WorkDay::createNewWorkDay($shop, $user, 1);
            if (empty($wDay)) {
                throw new ModelException("Shop::openShop error creating first work day", 1);
            }
            return $this->openSaveAndReturnBool($shop, $user);
        }

        if ($wDay->is_open === false || empty($wDay->closing_datetime)) {
            $locale = PoshubLocale::make();
            $wDay->is_open = false;
            $wDay->closing_datetime = $locale->getStringNowSystemDtfSystemTz();
            $wDay->updated_by_id = $user->id;
            if ($wDay->save() === false) {
                throw new ModelException("Shop::openShop error updating illegal work day", 2);
            }
        }

        $today = PoshubLocale::make()->getCarbonNowLocaleTz();
        if ($wDay->date === $today->format('Y-m-d')) {
            WorkDay::createNewWorkDay($shop, $user, $wDay->shift + 1);
            return $this->openSaveAndReturnBool($shop, $user);
        }
        WorkDay::createNewWorkDay($shop, $user, 1);
        return $this->openSaveAndReturnBool($shop, $user);
    }

    /**
     * @param  Shop           $shop
     * @param  User           $user
     * @return bool
     * @throws ModelException
     */
    public function closeShop(Shop $shop, User $user): bool
    {
        if ($shop->is_open === false) {
            return false;
        }
        $wDay = WorkDay::where('shop_id', '=', $shop->id)->first();

        if (empty($wDay)) {
            throw new ModelException(
                "Shop::closeShop error, impossible close a shop without work day",
                3
            );
        }

        if ($wDay->is_open === false || empty($wDay->closing_datetime) === false) {
            $this->closeSaveAndReturnBool($shop, $user);
            throw new ModelException(
                "Shop::closeShop error, closed a shop with a work day already closed. Shop id: " . $shop->id,
                4
            );
        }

        $today = PoshubLocale::make()->getCarbonNowLocaleTz();
        if ($wDay->date === $today->format('Y-m-d')) {
            WorkDay::createNewWorkDay($shop, $user, $wDay->shift + 1);
            return $this->openSaveAndReturnBool($shop, $user);
        }
        WorkDay::createNewWorkDay($shop, $user, 1);
        return $this->openSaveAndReturnBool($shop, $user);
    }

    public function openDeliveryShop(Shop $shop, User $user): bool
    {
        if ($shop->is_delivering ===  true) {
            return false;
        }

        $shop->is_delivering = true;
        $shop->updated_by_id = $user->id;
        return $shop->save();
    }

    public function closeDeliveryShop(Shop $shop, User $user): bool
    {
        if ($shop->is_delivering === false) {
            return false;
        }

        $shop->is_delivering = false;
        $shop->updated_by_id = $user->id;
        return $shop->save();
    }

    protected function openSaveAndReturnBool(Shop $shop, User $user): bool
    {
        $shop->is_open = true;
        $shop->updated_by_id = $user->id;
        return $shop->save();
    }

    protected function closeSaveAndReturnBool(Shop $shop, User $user): bool
    {
        $shop->is_open = false;
        $shop->updated_by_id = $user->id;
        return $shop->save();
    }
}
