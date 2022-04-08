<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class OptionalGroup extends Model
{
    use HasFactory, HasEditHistory, SoftDeletes;

    protected $table = 'poshub_optional_groups';

    protected $with = ['products'];

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
        'number',
        'name',
        'description',
        'image',
        'is_active',
        'no_discount',
        'is_optional',
        'remarks',
        'type',
        'type_limit',
        'created_by_id',
        'updated_by_id'
    ];


    const CHECKBOX_TYPE = 'checkbox';
    const DROPDOWN_TYPE = 'dropdown';
    const RADIO_BUTTON_TYPE = 'radio_button';

    const TYPES = [
        self::CHECKBOX_TYPE => 'Checkbox',
        self::DROPDOWN_TYPE => 'Dropdown',
        self::RADIO_BUTTON_TYPE => 'Radio Button'
    ];

    const TYPES_LIMITS = [
        self::CHECKBOX_TYPE => 20,
        self::DROPDOWN_TYPE => 1,
        self::RADIO_BUTTON_TYPE => 1,
    ];

    public static function getTypesForSelect(): array
    {
        $data = [];

        foreach (self::TYPES as $value => $text) {
            $data[] = ['text' => $text, 'value' => $value];
        }

        return $data;
    }

    public static function getTypesLimitsForSelect(): array
    {
        $data = [];

        foreach (self::TYPES_LIMITS as $key => $maxNumber) {
            for ($number = 1; $number <= $maxNumber; $number++) {
                $data[$key][] = ['text' => $number, 'value' => $number];
            }
        }

        return $data;
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'poshub_optional_group_products',
            'optional_group_id',
            'product_id'
        )
            ->withPivot('id', 'is_active','drag_order')
            ->withTimestamps();
    }
}
