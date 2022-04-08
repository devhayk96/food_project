<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class Product extends Model
{

    use HasFactory, HasEditHistory;

    protected $table = 'poshub_products';
    protected $hidden = [
        'is_receipt',
//        'is_kitchen',
        'is_sticker',
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
    protected $primaryKey = 'id';
    protected $with = ['vats', 'product_subgroups'];
    protected $fillable = [
        'id',
        'product_subgroup_id',
        'vat_id',
        'article_number',
        'name',
        'image',
//        'description_1',
        'description_2',
        'description_3',
        'main_product',
        'ean',
        'is_receipt',
        'is_kitchen',
        'is_sticker',
        'price',
        'status',
        'is_blocked',
        'is_various',
        'is_open_number',
        'is_open_price',
        'restock',
        'course',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function vats(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'vat_id', 'id');
    }

    public function product_subgroups(): BelongsTo
    {
        return $this->belongsTo(ProductSubgroup::class, 'product_subgroup_id', 'id');
    }

    public function optionGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionalGroup::class,
            'poshub_optional_group_products',
            'product_id',
            'optional_group_id'
        )
            ->withPivot('id', 'is_active')
            ->withTimestamps();
    }

}
