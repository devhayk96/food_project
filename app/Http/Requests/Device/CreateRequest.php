<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:poshub_devices,name'],
            'shop_id' => ['required', 'integer', 'exists:poshub_shops,id'],
            'orders_auto_refresh_time' => ['nullable', 'integer', 'in:'. implode(',', Device::ORDERS_AUTO_REFRESH_TIMES)]
        ];
    }

    public function messages()
    {
        return [
            'shop_id.required' => 'The shop field is required.',
        ];
    }
}
