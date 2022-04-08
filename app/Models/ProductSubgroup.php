<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class ProductSubgroup extends Model
{
    use HasFactory;

    protected $table = 'poshub_product_subgroups';
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'created_by_id',
        'updated_by_id',
    ];
    protected $with = ['product_groups'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'product_group_id',
        'number',
        'name',
        'image',
        'description',
        'is_blocked',
        'weight',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function product_groups(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_subgroup_id', 'id');
    }

    public function kitchens()
    {
        return $this->belongsToMany(
            Kitchen::class,
            'poshub_kitchen_subgroup',
            'subgroup_id',
            'kitchen_id'
        )
            ->withPivot('id', 'drag_order')
            ->withTimestamps();
    }

}
