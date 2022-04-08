<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateLaratrustTables();

        $config = Config::get('laratrust_seeder.roles_structure');

        if ($config === null) {
            $this->command->error("The configuration has not been published. Did you run `php artisan vendor:publish --tag=\"laratrust-seeder\"`");
            $this->command->line('');
            return false;
        }

        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        $n = 0;
        foreach ($config as $key => $modules) {
            $role = Role::where('name', $key)->first();
            if (!$role) {
                $role = Role::create([
                    'name' => $key,
                    'display_name' => ucwords(str_replace('_', ' ', $key)),
                    'description' => ucwords(str_replace('_', ' ', $key))
                ]);
            }
            if ($n == 0) {
                $highestRole = $role;
            }
            $n ++;

            $permissions = [];

            $this->command->info('Creating Role '. strtoupper($key));

            foreach ($modules as $module => $value) {
                foreach (explode(',', $value) as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permissions[] = \App\Models\Permission::firstOrCreate([
                        'name' => $module . '-' . $permissionValue,
                        'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                        'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                    ])->id;

                    $this->command->info('Creating Permission to '.$permissionValue.' for '. $module);
                }
            }

            $role->permissions()->sync($permissions);

            if (Config::get('laratrust_seeder.create_users')) {
                $this->command->info("Creating '{$key}' user");
                $user = \App\Models\User::create([
                    'name' => ucwords(str_replace('_', ' ', $key)),
                    'email' => $key.'@palms.nl',
                    'password' => bcrypt('password')
                ]);
                $user->attachRole($role);
            }
        }

        $defaultUser = User::where('email', config('app.cli_system_user_email'))->first();
        if ($defaultUser && !empty($highestRole) && !$defaultUser->hasRole($highestRole->name)) {
            $defaultUser->attachRole($highestRole);
        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return  void
     */
    public function truncateLaratrustTables()
    {
        $this->command->info('Truncating User, Role and Permission tables');
        Schema::disableForeignKeyConstraints();

        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();

        if (Config::get('laratrust_seeder.truncate_tables')) {
            DB::table('roles')->truncate();
            DB::table('permissions')->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }
}
