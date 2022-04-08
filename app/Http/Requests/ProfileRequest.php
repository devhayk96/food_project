<?php

namespace App\Http\Requests;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('profile-update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = Auth::id();
        $statuses = array_keys(User::getAllStatuses());

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $id, 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'phone' => ['nullable', 'string', 'min:6', 'max:25'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'min:2', 'max:25'],
            'address_1' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:' . implode(',', $statuses)],
            'number_of_lines' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
        ];
    }
}
