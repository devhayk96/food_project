<?php

namespace App;

use App\Models\User;
use Illuminate\Support\Facades\App;

trait HasCliUser
{
    public function getCurrentUserOrCli(): ?User
    {
        $email = config('app.cli_system_user_email');
        return User::where('email', $email)->firstOrFail();
        /*if (App::runningInConsole()) {
            $email = config('app.cli_system_user_email');
            return User::where('email', $email)->firstOrFail();
        }
        return auth()->user();*/
    }
}
