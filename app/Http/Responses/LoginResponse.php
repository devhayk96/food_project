<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        $user->timestamps = false;
        $user->last_login_ip = $request->ip();
        $user->last_login_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();
    }
}