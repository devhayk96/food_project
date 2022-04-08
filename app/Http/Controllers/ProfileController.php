<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:profile-read')->only('show');
        $this->middleware('permission:profile-update')->only('edit', 'update');
        $this->middleware('permission:profile-delete')->only('destroy');
    }

    public function show()
    {
        $user = User::with(['country', 'createdByUser', 'updatedByUser'])
            ->whereId(Auth::id())
            ->first();

        if (!$user) {
            abort(404);
        }

        return response([
            'user' => $user->toArray(),
            'all_statuses' => User::getAllStatuses(),
        ]);
    }

    public function edit()
    {
        $user = User::with(['country'])
            ->whereId(Auth::id())
            ->first();

        if (!$user) {
            abort(404);
        }

        return response([
            'user' => $user->toArray(),
            'all_statuses' => User::getAllStatusesAssoc(),
            'roles' => [],
            'countries' => Country::orderBy('name')->get()->toArray(),
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(404);
        }

        $updatableData = $request->validated();
        if (!empty($updatableData['password'])) {
            $updatableData['password'] = Hash::make($updatableData['password']);
        }
        unset($updatableData['role']);
        $updatableData['updated_by_id'] = Auth::user()->id;
        User::where('id', Auth::id())->update($updatableData);

        return response([
            'success' => true
        ]);
    }

    public function destroy()
    {
        $user = Auth::user();
        if (!$user) {
            abort(404);
        }

        $user->delete();

        return response([
            'success' => true
        ]);
    }
}
