<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class MenuCategoryLink extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menu_category_links';

    protected $with = ['category', 'products'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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
        'menu_category_id',
        'is_blocked',
        'weight',
        'created_by_id',
        'updated_by_id'
    ];

    public function category(): BelongsTo
    {
        return $this->BelongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->HasMany(MenuCategoryProduct::class, 'menu_category_id', 'id');
    }
}
