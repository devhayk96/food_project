<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderSourceType
 * @package App\Models
 * @mixin Builder
 */
class OrderSourceType extends Model
{
    public const FOODYX = 'pos';
    public const FOODYX_CREDENTIALS = 'App\Entities\FoodyxCredentials';

    public const THUISBEZORGD = 'thuisbezorgd';
    public const THUISBEZORGD_CREDENTIALS = 'App\Entities\ThuisbezorgdCredentials';

    public const UBEREATS = 'ubereats';
    public const UBEREATS_CREDENTIALS = 'App\Entities\UberEatsCredentials';

    public const WEBSHOP_API_CREDENTIALS = [
        self::FOODYX => self::FOODYX_CREDENTIALS,
        self::THUISBEZORGD => self::THUISBEZORGD_CREDENTIALS,
        self::UBEREATS => self::UBEREATS_CREDENTIALS,
    ];

    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_order_source_types';

    protected $fillable = [
        'code',
        'name',
        'client_class',
        'credentials_class',
        'is_active',
        'created_by_id',
        'updated_by_id'
    ];

    public function orderSources(): HasMany
    {
        return $this->hasMany(OrderSourceShop::class);
    }

    public function getShopIdsAttribute()
    {
        return count($this->orderSources) > 0 ? $this->orderSources()->pluck('shop_id') : [];
    }
}
