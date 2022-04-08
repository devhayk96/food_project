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
class MenuCategory extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_menu_categories';

    protected $with = [];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
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
        'name',
        'description',
        'image',
        'is_blocked',
        'is_food',
        'is_drink',
        'created_by_id',
        'updated_by_id'
    ];
}
