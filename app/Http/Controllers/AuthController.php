<?php

namespace App\Http\Controllers;

use App\Helpers\Util;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shops-create')->only('issueSimpleToken');
        $this->middleware('permission:shops-update')->only('revokeCurrentToken');
    }
    /**
     * Issuing a simple token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function issueSimpleToken(Request $request)
    {
        return  $request->user()->createToken('simple-token')->plainTextToken;
    }

    /**
     * Revoking a the token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revokeCurrentToken(Request $request)
    {
        return $request->user()->currentAccessToken()->delete();
    }


    public function logout(Request $request)
    {
        return $request->user()->currentAccessToken()->delete();
    }

    /**
     * Login with API.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /*
            * =====================================================
            * #########      Authentication passed        #########
            * =====================================================
            */
            return $request->user()->createToken('simple-token')->plainTextToken;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginWithPinCode(Request $request): JsonResponse
    {
        if ($request->has('pin_code') && $request->has('device_name')) {
            $user = User::query()->where(['pin_code' => $request->get('pin_code')])->first();

            if ($user && Auth::loginUsingId($user->id)) {
                if ($device = Device::query()->with('shop')->where(['name' => $request->get('device_name')])->first()) {

                    if (!in_array($device->shop->id, $user->shop_ids)) {
                        return response()->json(['success' => false, 'error' => 'You have not access to this shop device'], 403);
                    }

                    return response()->json(['success' => true, 'token' => $request->user()->createToken('simple-token')->plainTextToken]);
                }
                return response()->json(['success' => false, 'error' => 'Invalid device ID'], 404);
            } else {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }
        }

        return response()->json(['success' => false, 'error' => 'Please send pin code'], 422);
    }

}
