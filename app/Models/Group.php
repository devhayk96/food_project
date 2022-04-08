<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Group
 * @package App\Models
 * @mixin Builder
 */
class Group extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_groups';

    protected $fillable = [
        'code',
        'name',
        'description',
        'created_by_id',
        'updated_by_id'
    ];
}
