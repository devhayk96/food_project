<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class MenuSourceShop extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menu_source_shops';

    protected $with = ['source', 'shop'];

    protected $hidden = [];

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'source_id',
        'shop_id',
        'is_blocked',
        'created_by_id',
        'updated_by_id'
    ];

    public function source(): BelongsTo
    {
        return $this->BelongsTo(OrderSourceType::class, 'source_id', 'id');
    }

    public function shop(): BelongsTo
    {
        return $this->BelongsTo(Shop::class, 'shop_id', 'id');
    }
}
