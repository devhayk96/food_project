<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class OrderSourceShop extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_order_source_shops';

    protected $with = ['orderSourceType'];

    protected $fillable = [
        'order_source_type_id',
        'shop_id',
        'code',
        'name',
        'is_active',
        'is_auto_accept',
        'credentials',
        'created_by_id',
        'updated_by_id'
    ];

    /**
     * The shops that belong to the order source.
     */
    public function shops()
    {
        return $this->belongsTo(
            'App\Models\Shop',
            'shop_id',
            'id'
        );
    }

    public function orders()
    {
        return $this->hasMany(
            'App\Models\Order',
            'order_source_id',
            'id'
        );
    }

    public function orderSourceType()
    {
        return $this->belongsTo('App\Models\OrderSourceType');
    }

    static public function getWhereSourceAndSourceTypeAreActive()
    {
        return OrderSourceShop::with('orderSourceType', 'shops')
            ->where('is_active', 1)
            ->whereHas('orderSourceType', function($q){
                $q->where('is_active', 1);
            })
            ->get();
    }
}
