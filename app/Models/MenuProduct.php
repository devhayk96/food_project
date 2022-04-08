<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class MenuProduct extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menu_products';

    protected $with = ['product', 'optionalGroups'];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
    ];

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'product_id',
        'price',
        'is_blocked',
        'created_by_id',
        'updated_by_id'
    ];

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id', 'id');
    }

    public function menuCategoryProducts(): HasMany
    {
        return $this->HasMany(MenuCategoryProduct::class, 'menu_product_id', 'id');
    }

    public function optionalGroups(): HasMany
    {
        return $this->HasMany(PoshubCategoryProductOptionGroups::class, 'product_id', 'product_id');
    }

}
