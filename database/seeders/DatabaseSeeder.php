<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'System',
            'email' => config('app.cli_system_user_email'),
            'password' => Hash::make(
                config('app.cli_system_user_pass')
            ),
            'created_by_id' => 1,
            'updated_by_id' => 1
        ]);

        $user->created_by_id = $user->id;
        $user->updated_by_id = $user->id;
        $user->save();

        // Create new API user
        $api_user = User::create([
            'name' => 'API-user',
            'email' => 'api-test@poshub.nl',
            'password' => Hash::make('V*U9t2vuN7'),
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ]);

        $api_user->created_by_id = $user->id;
        $api_user->updated_by_id = $user->id;

        $this->call([
            OrderSourceTypeSeeder::class,
            PaymentMethodSeeder::class,
            OrderStatusSeeder::class,
            OrderTypeSeeder::class,
            CourierTypeSeeder::class,
            AddressSeeder::class,
            ShopSeeder::class,
            OrderSourceSeeder::class,
            WorkHoursSeeder::class,
            CountriesSeeder::class,
            PermissionObjectsSeeder::class,
        ]);
    }
}
