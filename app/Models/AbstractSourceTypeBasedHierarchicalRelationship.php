<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Abstract Class AbstractSourceTypeBasedHierarchicalRelationship
 * @package App\Models
 * @mixin Builder
 */
abstract class AbstractSourceTypeBasedHierarchicalRelationship extends Model
{
    protected $fillable = [
        'code',
        'name',
        'source_type_id',
        'parent_id',
        'is_active',
        'created_by_id',
        'updated_by_id'
    ];

    protected $with = ['sourceType'];

    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(OrderSourceType::class, 'source_type_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function getPrimitive(): AbstractSourceTypeBasedHierarchicalRelationship
    {
        if (empty($this->parent_id)) {
            return $this;
        }

        return self::find($this->parent_id);
    }
}
