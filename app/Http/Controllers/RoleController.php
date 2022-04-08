<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles-read')->only('index', 'show');
        $this->middleware('permission:roles-create')->only('create', 'store');
        $this->middleware('permission:roles-update')->only('edit', 'update');
        $this->middleware('permission:roles-delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::with(['createdByUser', 'updatedByUser'])
            ->orderBy('id')
            ->get()
            ->toArray();

        return response([
            'roles' => $roles
        ]);
    }

    public function show($id)
    {
        $role = Role::with(['createdByUser', 'updatedByUser'])
            ->whereId($id)
            ->first();

        if (!$role) {
            abort(404);
        }

        return response([
            'role' => $role->toArray(),
        ]);
    }

    public function create()
    {
        return response([
            'role' => (new Role)->toArray(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $creatableData = $request->validated();
        $creatableData['created_by_id'] = Auth::user()->id;
        $role = Role::create($creatableData);

        return response([
            'success' => true,
            'id' => $role->id
        ]);
    }

    public function edit($id)
    {
        $role = Role::find($id);
        if (!$role || $role->name == Role::SUPERADMIN) {
            abort(404);
        }

        return response([
            'role' => $role->toArray(),
        ]);
    }

    public function update(RoleRequest $request, $id)
    {
        $role = Role::find($id);
        if (!$role || $role->name == Role::SUPERADMIN) {
            abort(404);
        }

        $updatableData = $request->validated();
        $updatableData['updated_by_id'] = Auth::user()->id;
        Role::where('id', $id)->update($updatableData);

        return response([
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role || $role->name == Role::SUPERADMIN) {
            abort(404);
        }

        $role->delete();

        return response([
            'success' => true
        ]);
    }
}
