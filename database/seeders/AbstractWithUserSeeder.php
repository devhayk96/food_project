<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AbstractWithUserSeeder extends Seeder
{
    public function getSystemUser(): User
    {
        return User::where(['email' => 'posadmin@poshub.io'])->firstOrFail();
    }
}
