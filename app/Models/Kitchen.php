<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class Kitchen extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_kitchens';

    protected $with = ['shop'];
    protected $hidden = [];
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id',
        'number',
        'name',
        'is_blocked',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function subGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductSubgroup::class,
            'poshub_kitchen_subgroup',
            'kitchen_id',
            'subgroup_id',
        )
            ->withPivot('id', 'drag_order')
            ->withTimestamps();
    }

}
