<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class MenuCategoryProduct extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menu_category_products';

    protected $with = ['menuProduct'];

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
        'name',
        'menu_product_id',
        'menu_category_id',
        'is_blocked',
        'weight',
        'created_by_id',
        'updated_by_id'
    ];

    public function menuProduct(): BelongsTo
    {
        return $this->BelongsTo(MenuProduct::class, 'menu_product_id', 'id');
    }

}
