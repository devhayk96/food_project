<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class User extends Authenticatable
{
    use LaratrustUserTrait, HasFactory, Notifiable, HasApiTokens, HasEditHistory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pin_code',
        'phone',
        'country_id',
        'city',
        'zip',
        'address_1',
        'address_2',
        'status',
        'last_login_ip',
        'last_login_at',
        'created_by_id',
        'updated_by_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const DEFAULT_PIN_CODE_LENGTH = 6;
    const PIN_CODE_LENGTHS = [4, 6];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['attached_roles'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(self::class, 'created_by_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(self::class, 'updated_by_id', 'id');
    }

    public function getAttachedRolesAttribute()
    {
        return $this->roles()->count() != 0 ? $this->roles->unique('name')->pluck('name')->implode(', ') : '';
    }

    public function getPinCodeAttribute($value)
    {
        return "$value";
    }

    public static function getAllStatuses()
    {
        return [
            Common::STATUS_ACTIVE => 'Active',
            Common::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public static function getAllStatusesAssoc()
    {
        $statuses = [];
        foreach (self::getAllStatuses() as $status => $text) {
            $statuses[] = [
                'status' => $status,
                'text' => $text,
            ];
        }
        return $statuses;
    }

    /**
     * @return BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany(
            Shop::class,
            'poshub_shop_user',
            'user_id',
            'shop_id'
        );
    }

    /**
     * @return array
     */
    public function getShopIdsAttribute(): array
    {
        return count($this->shops) > 0 ? $this->shops()->pluck('shop_id')->toArray() : [];
    }
}
