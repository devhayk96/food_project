<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 * @package App\Models
 * @mixin Builder
 */
class Order extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_orders';

    protected $with = ['orderStatus', 'orderSource', 'orderType', 'shop', 'customer', 'paymentMethod', 'orderProducts', 'discounts', 'courierType', 'cancellationReason', 'extraCostVat'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_number',
        'daily_order_number',
        'order_status_id',
        'order_source_id',
        'order_type_id',
        'shop_id',
        'customer_id',
        'courier_type_id',
        'delivery_cost',
        'delivery_vat_amount',
        'delivery_remarks',
        'payment_method_id',
        'is_asap',
        'order_datetime',
        'requested_time',
        'order_datetime',
        'actual_delivery_time',
        'total_discount',
        'total_price',
        'tip_price',
        'is_paid',
        'order_remark',
        'order_json',
        'customer_json',
        'address_json',
        'fulfillment_issues',

        'address_id',
        'extra_cost_vat_id',
        'delivery_vat_id',
        'table_id',
        'cancellation_id',
        'cancellation_comment',
        'extra_cost_vat_amount',
        'extra_costs',
        'is_printed',
        'is_pos_sync',
        'pays_with',
        'payment_session_id',
        'number_of_guests',
        'course',
        'distance',
        'invoice_id',
        'courier_left_at',
        'courier_back_at',

        'kitchen_id',
        'courier_id',
        'is_discount_percentage',

        'created_by_id',
        'updated_by_id',
        'piggy_stage',
        'piggy_physical_reward',

    ];

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id');
    }

    public function orderSource(): BelongsTo
    {
        return $this->belongsTo(OrderSourceShop::class, 'order_source_id', 'id');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type_id', 'id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function kitchenProducts(): HasMany
    {
        return $this->orderProducts()->whereHas('productDetails', function ($query) {
            $query->where('is_kitchen', 1);
        });
    }


    /*public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'order_id', 'delivery_id');
    }*/

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function courierType(): BelongsTo
    {
        return $this->belongsTo(CourierType::class, 'courier_type_id', 'id');
    }

    public function cancellationReason(): BelongsTo
    {
        return $this->belongsTo(CancellationReason::class, 'cancellation_id', 'id');
    }

    public function deliveryVat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'delivery_vat_id', 'id');
    }

    public function extraCostVat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'extra_cost_vat_id', 'id');
    }

    public function discounts(): hasMany
    {
        return $this->hasMany(OrderDiscount::class, 'order_id', 'id');
        /*return $this->belongsToMany(
            Discount::class,
            'poshub_order_discount',
            'order_id',
            'discount_id',
        );*/
    }

    public function statusUpdates(): HasMany
    {
        return $this->hasMany(OrderStatusUpdate::class, 'order_id', 'id');
    }

    /**
     * @param $status
     * @return string
     */
    public static function statusColor($status): string
    {
        switch ($status) {
            case OrderStatus::NEW:
            case OrderStatus::ACCEPTED:
                return 'open';
            case OrderStatus::KITCHEN:
                return 'cooking';
            case OrderStatus::FINISHED:
                return 'done';
            default: return $status;
        }
    }

}
