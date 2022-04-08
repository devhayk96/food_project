<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionObject;
use App\Models\Role;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions-read')->only('index');
        $this->middleware('permission:permissions-update')->only('update');
    }

    public function index()
    {
        $roles = Role::with(['permissions'])
            ->orderBy('id')
            ->get();
        $permissionObjects = PermissionObject::all();
        $permissionsMap = config('laratrust_seeder.permissions_map');

        foreach ($roles as $role) {
            $grouppedPermissions = [];
            $rolePermissions = $role->permissions->toArray();
            foreach ($permissionObjects as $permissionObject) {
                $grouppedPermissions[$permissionObject['name']] = [
                    'display_name' => $permissionObject['display_name'],
                    'data' => []
                ];
                foreach ($permissionsMap as $permissionKey => $permissionSuffix) {
                    $permissionExistence = array_search($permissionObject['name'] . '-' . $permissionSuffix, array_column($rolePermissions, 'name'));
                    $grouppedPermissions[$permissionObject['name']]['data'][$permissionKey] = [
                        'name' => $permissionSuffix,
                        'value' => $permissionExistence !== false,
                    ];
                }
            }
            $role->grouppedPermissions = $grouppedPermissions;
        }

        return response([
            'roles' => $roles->toArray()
        ]);
    }

    public function update(Request $request)
    {
        $roles = $request->all();
        if (!$roles) {
            throw ValidationException::withMessages([
                'permissions' => 'You sent an empty data.',
            ]);
        }

        $this->truncateLaratrustTables();

        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        foreach ($roles as $roleData) {
            $roleName = $roleData['name'] ?? '';
            if ($roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $permissionsData = $roleData['grouppedPermissions'] ?? [];
                    $permissions = [];
                    foreach ($permissionsData as $object => $objPermissionsData) {
                        $objPermissionsData = $objPermissionsData['data'] ?? [];
                        foreach ($objPermissionsData as $permission => $value) {
                            if (!!($value['value'] ?? false)
                                || ($roleName == Role::SUPERADMIN &&
                                    (in_array($object, ['users', 'roles']) || ($object == 'permissions' && in_array($permission, ['r', 'u'])))
                                )
                            ) {
                                $permissionName = $mapPermission->get($permission);

                                $permissions[] = Permission::firstOrCreate([
                                    'name' => $object . '-' . $permissionName,
                                    'display_name' => ucfirst($permissionName) . ' ' . ucfirst($object),
                                    'description' => ucfirst($permissionName) . ' ' . ucfirst($object),
                                ])->id;
                            }
                        }
                    }

                    $role->permissions()->sync($permissions);
                }
            }
        }

        return response([
            'success' => true
        ]);
    }

    private function truncateLaratrustTables()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();

        if (Config::get('laratrust_seeder.truncate_tables')) {
            DB::table('permissions')->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }
}
