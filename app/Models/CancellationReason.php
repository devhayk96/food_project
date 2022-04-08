<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */

class CancellationReason extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_cancellation_reasons';

    protected $with = [];
    protected $hidden = [];
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'description',
        'is_explanation_required',
        'is_blocked',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id'
    ];}
