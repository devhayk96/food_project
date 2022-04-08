<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentMethod
 * @package App\Models
 * @mixin Builder
 */
class PaymentMethod extends AbstractSourceTypeBasedHierarchicalRelationship
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_payment_methods';

    public function orders(): HasMany
    {
        return $this->hasMany('App\Models\Order', 'payment_method_id', 'id');
    }
}
