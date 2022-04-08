<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountSettings extends Model
{
    use HasFactory;

    protected $table = 'account_settings';

    protected $hidden = ['id','created_at','updated_at'];

    protected $fillable = [
        'logo',
        'background_image',
        'background_color',
        'text_color',
        'button_color',
        'icon_color',
        'link_color',
        'logo_width',
        'logo_height',
        'logo_original_height',
        'logo_original_width',
        'tabs_header_color',
        'header_background_color',
        'piggy_checkbox',
        'content_image',
        'secret_token',
        'shop_id',
        'client_id',
    ];

}
