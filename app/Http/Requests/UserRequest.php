<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('users-create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('user');
        $roles = Role::orderBy('id');
        $roleSuperadmin = Role::SUPERADMIN;
        if (!Auth::user()->hasRole($roleSuperadmin)) {
            $roles = $roles->where('name', '!=', $roleSuperadmin);
        }
        if (!Auth::user()->hasPermission('users-update')) {
            $unAvailableRoles = Role::whereHas('permissions', function ($query) {
                    $query->where('name', 'users-update');
                })
                ->pluck('name')
                ->toArray();
            $roles = $roles->whereNotIn('name', $unAvailableRoles);
        }
        if (Auth::user()->id == $id) {
            $roles = $roles->where('name', Auth::user()->roles()->first()->name);
        }
        $roles = $roles->pluck('name')->toArray();

        $statuses = array_keys(User::getAllStatuses());
        $passwordRules = ['string', 'min:6', 'max:255'];
        $pinCodeRules = ['required', function ($attribute, $value, $callback) {
            if (!(strlen($value) === 4 || strlen($value) === 6)) {
                $callback('Pin Code length must be 4 or 6 digits.');
            }
        }];
        if ($id) {
            $passwordRules[] = 'nullable';
            $pinCodeRules[] = 'unique:users,pin_code,'. $id;
        } else {
            $passwordRules[] = 'required';
            $pinCodeRules[] = 'unique:users,pin_code';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $id, 'max:255'],
            'password' => $passwordRules,
            'pin_code' => $pinCodeRules,
            'pin_code_length' => ['required', 'in:'. implode(',', User::PIN_CODE_LENGTHS)],
            'phone' => ['nullable', 'string', 'min:6', 'max:25'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'min:2', 'max:25'],
            'address_1' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:' . implode(',', $roles)],
            'status' => ['required', 'in:' . implode(',', $statuses)],
        ];
    }
}
