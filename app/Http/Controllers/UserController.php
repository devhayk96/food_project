<?php

namespace App\Http\Controllers;

use App\Helpers\Util;
use App\Http\Requests\UserRequest;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users-read')->only('index', 'show');
        $this->middleware('permission:users-create')->only('create', 'store');
        $this->middleware('permission:users-update')->only('edit', 'update');
        $this->middleware('permission:users-delete')->only('destroy');
    }

    public function index()
    {
        $users = User::with(['country', 'createdByUser', 'updatedByUser'])
            ->orderBy('id')
            ->get()
            ->toArray();

        return response([
            'users' => $users,
            'all_statuses' => User::getAllStatuses(),
        ]);
    }

    public function show($id)
    {
        $user = User::with(['country', 'createdByUser', 'updatedByUser'])
            ->whereId($id)
            ->first();

        if (!$user) {
            abort(404);
        }

        return response([
            'user' => $user->toArray(),
            'all_statuses' => User::getAllStatuses(),
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('id');
        $roleSuperadmin = Role::SUPERADMIN;
        if (!Auth::user()->hasRole($roleSuperadmin)) {
            $roles = $roles->where('name', '!=', $roleSuperadmin);
        }
        if (!Auth::user()->hasPermission('users-update')) {
            $unAvailableRoles = Role::query()
                ->whereHas('permissions', function ($query) {
                    $query->where('name', 'users-update');
                })
                ->pluck('name')
                ->toArray();
            $roles = $roles->whereNotIn('name', $unAvailableRoles);
        }
        $roles = $roles->get()->toArray();
        $user = new User();
        $user->pin_code_length = User::DEFAULT_PIN_CODE_LENGTH;

        return response([
            'user' => $user->toArray(),
            'all_statuses' => User::getAllStatusesAssoc(),
            'roles' => $roles,
            'countries' => Country::orderBy('name')->get()->toArray(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $creatableData = $request->validated();
        $creatableData['password'] = Hash::make($creatableData['password']);
        unset($creatableData['role']);
        $creatableData['created_by_id'] = Auth::user()->id;
        $user = User::create($creatableData);

        $user->attachRole($request->get('role'));

        return response([
            'success' => true,
            'id' => $user->id
        ]);
    }

    public function edit($id)
    {
        $user = User::with(['country'])
            ->whereId($id)
            ->first();
        $roleSuperadmin = Role::SUPERADMIN;

        if (!$user || ($user->hasRole($roleSuperadmin) && !Auth::user()->hasRole($roleSuperadmin))) {
            abort(404);
        }

        $roles = [];
        if (!$user->hasRole($roleSuperadmin) || Auth::user()->hasRole($roleSuperadmin)) {
            $roles = Role::orderBy('id');
            if (!Auth::user()->hasRole($roleSuperadmin)) {
                $roles = $roles->where('name', '!=', $roleSuperadmin);
            }
            if (Auth::user()->id == $id) {
                $roles = $roles->where('name', Auth::user()->roles()->first()->name);
            }
            $roles = $roles->get()->toArray();
        }

        $user->shop_ids = $user->shopIds;

        return response([
            'user' => $user->toArray(),
            'all_statuses' => User::getAllStatusesAssoc(),
            'roles' => $roles,
            'countries' => Country::orderBy('name')->get()->toArray(),
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if (!$user || ($user->hasRole(Role::SUPERADMIN) && !Auth::user()->hasRole(Role::SUPERADMIN))) {
            abort(404);
        }

        $updatableData = $request->validated();
        if (!empty($updatableData['password'])) {
            $updatableData['password'] = Hash::make($updatableData['password']);
        }
        unset($updatableData['role']);
        $updatableData['updated_by_id'] = Auth::user()->id;
        $updatableData['pin_code'] = $request->get('pin_code');
        User::where('id', $id)->update($updatableData);

        $user->shops()->sync($request->get('shop_ids'));

        $user->roles()->detach();
        $user->attachRole($request->get('role'));

        return response([
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user || ($user->hasRole(Role::SUPERADMIN) && !Auth::user()->hasRole(Role::SUPERADMIN))) {
            abort(404);
        }

        $user->delete();

        return response([
            'success' => true
        ]);
    }


    /**
     * Get new unique pin code for user
     *
     * @return string
     */
    public function getNewPinCode(): string
    {
        return Util::generatePinCode();
    }

    /**
     * Get User pin code max length
     *
     * @return JsonResponse
     */
    public function getPinCodeMaxLength(): JsonResponse
    {
        return response()->json(max(User::PIN_CODE_LENGTHS));
    }


    /**
     * Get User pin code max length
     *
     * @return JsonResponse
     */
    public function getPinCodePossibleLengths(): JsonResponse
    {
        return response()->json(User::PIN_CODE_LENGTHS);
    }

}
