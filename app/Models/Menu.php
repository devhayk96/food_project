<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class Menu extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menus';

    protected $with = [];

    protected $hidden = ['menuCategories', 'menuSourceShops', 'menuProducts'];

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_blocked',
        'created_by_id',
        'updated_by_id'
    ];

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategoryLink::class, 'menu_id', 'id');
    }
    public function menuSourceShops(): HasMany
    {
        return $this->hasMany(MenuSourceShop::class, 'menu_id', 'id');
    }

    public function menuProducts(): HasMany
    {
        return $this->HasMany(MenuProduct::class, 'menu_id', 'id');
    }
}
