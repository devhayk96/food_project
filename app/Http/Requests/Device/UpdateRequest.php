<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'exists:poshub_devices,id'],
            'name' => ['required', 'unique:poshub_devices,name,'. $this->id],
            'code' => ['required', 'unique:poshub_devices,code,'. $this->id],
            'shop_id' => ['required', 'integer', 'exists:poshub_shops,id'],
            'orders_auto_refresh_time' => ['nullable', 'integer', 'in:'. implode(',', Device::ORDERS_AUTO_REFRESH_TIMES)],
        ];
    }

    public function messages()
    {
        return [
            'shop_id.required' => 'The shop field is required.',
        ];
    }
}
