<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class OptionalGroupProduct extends Pivot
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_optional_group_products';

    protected $with = [];

    protected $hidden = [];

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'optional_group_id',
        'drag_order',
        'product_id',
        'is_active',
        'created_by_id',
        'updated_by_id'
    ];

    public function setCreatedByIdAttribute()
    {
        $this->created_by_id = Auth::id();
    }

    public function setUpdatedByIdAttribute()
    {
        $this->updated_by_id = Auth::id();
    }

}
