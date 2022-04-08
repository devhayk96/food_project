<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class OrderProduct extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;
    protected $with = ['productDetails'];
    protected $table = 'poshub_order_products';
    protected $hidden = [];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'vat_id',
        'quantity',
        'unit_price',
        'total_price',
        'vat_amount',
        'is_discount_percentage',
        'status',
        'discount',
        'discounted_amount',
        'remarks',
        'created_by_id',
        'created_at',
        'updated_by_id',
        'updated_at',
        'deleted_at',
    ];

    public function productDetails(): hasOne
    {
        return $this->hasOne(Product::class,'id', 'product_id');
    }

    public function orders(): BelongsTo
    {
        return $this->BelongsTo('App\Models\Order', 'id', 'order_id');
    }

    /**
     * @param $status
     * @return string
     */
    public static function statusColor($status): string
    {
        switch ($status) {
            case 'new':
                return 'open';
            case 'finished':
                return 'done';
            default: return $status;
        }
    }

}
