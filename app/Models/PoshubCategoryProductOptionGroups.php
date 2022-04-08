<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PoshubCategoryProductOptionGroups extends Model
{
    use HasFactory;

    protected $with = ['OptionalGroup'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = ['menu_id', 'menu_category_id'];

    public function OptionalGroup(): HasMany
    {
        return $this->HasMany(OptionalGroup::class, 'id', 'optional_group_id');
    }
}
