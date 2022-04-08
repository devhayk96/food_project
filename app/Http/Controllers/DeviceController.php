<?php

namespace App\Http\Controllers;


use App\Helpers\Util;
use App\Http\Requests\Device\CreateRequest;
use App\Http\Requests\Device\UpdateRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:device-read')->only('index', 'show');
        $this->middleware('permission:device-create')->only('store');
        $this->middleware('permission:device-update')->only('update');
    }

    /**
     * List of devices
     **/
    public function index()
    {
        return DeviceResource::collection(Device::paginate(100));
    }

    /**
     * Device details
     *
     * @param int $id
     **/

    public function show($id)
    {
        return $this->executeShow($id, Device::class, DeviceResource::class);
    }

    /**
     * Device add function
     *
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->merge(['code' => Util::generateDeviceCode()]);
            Device::query()->create($request->only((new Device())->getFillable()));

            DB::commit();
            $response['status'] = 'success';
        } catch (\Exception $exception) {
            DB::rollBack();
            $response['status'] = 'error';
            $response['error_message'] = $exception->getMessage();
        }

        return response()->json($response);
    }

    /**
     * Device update function
     *
     * @param UpdateRequest $request
     * @param Device $device
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Device $device): JsonResponse
    {
        try {
            DB::beginTransaction();

            $device->update($request->only((new Device())->getFillable()));

            DB::commit();
            $response['status'] = 'success';
        } catch (\Exception $exception) {
            DB::rollBack();
            $response['status'] = 'error';
            $response['error_message'] = $exception->getMessage();
        }

        return response()->json($response);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            Device::destroy($id);
            $response['status'] = 'success';
            $response['message'] = 'Device deleted successfully';
        } catch (\Exception $exception) {
            $response['status'] = 'error';
            $response['error_message'] = $exception->getMessage();
        }

        return response()->json($response);
    }

    /**
     * @return JsonResponse
     */
    public function getNewCode(): JsonResponse
    {
        return response()->json(Util::generateDeviceCode());
    }


    public function deviceCheck(Request $request)
    {
        if ($request->has('deviceCode')) {
            try {
                $device = Device::query()
                    ->where('code', $request->get('deviceCode'))
                    ->select('name')
                    ->firstOrFail();
            } catch (\Exception $exception) {
                return response()->json(['status' => 'fail', 'message' => 'Invalid device ID'], 404);
            }
            return response()->json(['status' => 'success', 'deviceName' => $device->name, 'message' => 'Device ID is accepted. Please enter your pin code']);
        }

        return response()->json(['status' => 'fail', 'message' => 'Please send device ID'], 400);
    }

}
