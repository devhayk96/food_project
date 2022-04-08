<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('roles-create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $id,],
            'display_name' => ['required', 'string', 'max:255', 'unique:roles,display_name,' . $id,],
            'description' => ['required', 'string', 'max:255'],
        ];
    }
}
