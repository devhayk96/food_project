<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderSourceClientException;
use App\Exceptions\OrderSourceClientValidationException;
use App\Http\Resources\OrderResource;
use App\Http\Resources\Pos\OrderPosCollection;
use App\Locale\PoshubLocale;
use App\Models\OrderSourceShop;
use App\Models\OrderSourceType;
use App\Models\OrderStatus;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateOrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:orders-read')->only('getStoreStatus');
        $this->middleware('permission:orders-update')->only('acceptPoshubOrder', 'declinePosOrder');
        $this->middleware('permission:clients-update')->only('setStoreStatus');
    }

    public function receivePosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [OrderStatus::ACCEPTED],
            OrderStatus::RECEIVED
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function kitchenPosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [OrderStatus::RECEIVED],
            OrderStatus::KITCHEN
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function deliveryPosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [OrderStatus::KITCHEN],
            OrderStatus::DELIVERY
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function finishPosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [OrderStatus::DELIVERY],
            OrderStatus::FINISHED
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function declinePosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [],
            OrderStatus::DECLINED
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function cancelPosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [],
            OrderStatus::CANCELLED
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function errorPosOrder(Request $request, string $orderId)
    {
        $result = $this->updateStatus(
            $request,
            $orderId,
            [],
            OrderStatus::ERROR
        );
        return $this->returnOrderPosCollectionOrOther($result);
    }

    public function acceptPoshubOrder(Request $request, string $orderId)
    {
        $result = $this->acceptOrder($request, $orderId);
        return ($result instanceof Order)
            ? new OrderResource($result)
            : $result;
    }

    protected function acceptOrder(Request $request, string $orderId)
    {
        try {
            $this->currentUser = $request->user();
            $values = $request->validate([
                'accepted_datetime' => ['nullable', 'sometimes', 'date_format:Y-m-d H:i'],
            ]);
            $validator = Validator::make(
                ['orderId' => $orderId],
                ['orderId' => 'required|exists:poshub_orders,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $order = Order::findOrFail($orderId);

        $new = OrderStatus::where([
            'code' => OrderStatus::NEW,
            'parent_id' => null
        ])->firstOrFail();

        $error = OrderStatus::where([
            'code' => OrderStatus::ERROR,
            'parent_id' => null
        ])->firstOrFail();

        if ($order->orderStatus->id != $new->id && $order->orderStatus->id != $error->id) {
            return response()->json(
                [
                    'errors' => [
                        'order' => 'You can accept only order with status new or in error. This order status code is ' .
                            $order->orderStatus->code
                    ]
                ],
                400
            );
        }

        $acceptedDatetimeString = empty($values['accepted_datetime']) ? date_format(date_create(),'Y-m-d H:i') : $values['accepted_datetime'];

        if ($order->orderSource->orderSourceType->code != OrderSourceType::FOODYX) {
            try {
                $order->orderSource->orderSourceType->client_class::make()
                    ->initClient($order->orderSource, $order->shop)
                    ->acceptOrder($order, (array)$values);
            } catch (OrderSourceClientValidationException $exception) {
                return response()->json(
                    ['errors' => ['order' => $exception->getMessage()]],
                    400
                );
            } catch (OrderSourceClientException $exception) {
                return response()->json(
                    [
                        'errors' => [
                            'order' => 'Unexpected error updating external order source ' .
                                $order->orderSource->orderSourceType->code
                        ]
                    ],
                    200
                );
            }
        }

        $accepted = OrderStatus::where([
            'code' => OrderStatus::ACCEPTED,
            'parent_id' => null
        ])->firstOrFail();

        $order->order_status_id = $accepted->id;

        if (!is_null($acceptedDatetimeString)) {
            $order->requested_time = $this->locale->getCarbonFromLocale($acceptedDatetimeString)
                ->setTimezone($this->locale->poshubSystemTz)
                ->format($this->locale->poshubSystemDtFormat);
        }
        if (!$request->has('not_sync')) {
            $order->is_pos_sync = 1;
        }
        $order->save();
        $order->refresh();

        return $order;
    }

    public function errorOrder(Request $request, string $orderId)
    {
        $values = $this->validateRequest($request, [
            'message' => ['nullable', 'sometimes', 'string', 'min:3', 'max:256'],
        ]);
        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

    }

    public function declineOrder(Request $request, string $orderId)
    {

    }

    protected function updateStatus(Request $request, string $orderId, array $codesFrom, string $codeTo)
    {
        try {
            $this->currentUser = $request->user();
            $validator = Validator::make(
                ['orderId' => $orderId],
                ['orderId' => 'required|uuid|exists:poshub_orders,id']
            );
            $validator->validate();
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 400);
        }

        $order = Order::find($orderId);

        $fromStatuses = array_map(
            function ($codeForm) {
                return OrderStatus::where([
                    'code' => $codeForm,
                    'parent_id' => null
                ])->firstOrFail();
            },
            $codesFrom
        );

        $to = OrderStatus::where([
            'code' => $codeTo,
            'parent_id' => null
        ])->firstOrFail();

        $orderStatusIsValid = array_reduce(
            $fromStatuses,
            function (bool $carry, OrderStatus $status) use ($order) {
                return ($carry === true)
                    ? $carry
                    : ($order->orderStatus->id === $status->id);
            },
            false
        );

        if ($orderStatusIsValid === false && ($codeTo !== 'cancelled' && $codeTo !== 'declined'&& $codeTo !== 'error')) {
            return response()->json(
                [
                    'errors' => [
                        'order' => 'You cannot update an order status from ' . $order->orderStatus->name . ' to ' .
                            $to->name
                    ]
                ],
                400
            );
        }

        if ($order->orderSource->orderSourceType->code != OrderSourceType::FOODYX) {
            try {
                $remote = OrderStatus::where('parent_id', '=', $to->id)
                    ->where('source_type_id', '=', $order->orderSource->orderSourceType->id)
                    ->where('is_active', '=', 1)
                    ->count(['id']);

                if ($remote > 0) {
                    $client = $order->orderSource->orderSourceType->client_class::make()
                        ->initClient($order->orderSource, $order->shop);
                    if ($to->code === OrderStatus::KITCHEN) {
                            $client->kitchenOrder($order);
                    }
                    if ($to->code === OrderStatus::DELIVERY) {
                        $client->deliveryOrder($order);
                    }
                    if ($to->code === OrderStatus::FINISHED) {
                        $client->finishOrder($order);
                    }
                    if ($to->code === OrderStatus::DECLINED) {
                        $client->declineOrder($order);
                    }
                    if ($to->code === OrderStatus::CANCELLED) {
                        $client->cancelOrder($order);
                    }
                }
            } catch (OrderSourceClientValidationException $exception) {
                return response()->json(
                    ['errors' => ['order' => $exception->getMessage()]],
                    400
                );
            } catch (OrderSourceClientException $exception) {
                return response()->json(
                    [
                        'errors' => [
                            'order' => 'Unexpected error updating external order source ' .
                                $order->orderSource->orderSourceType->code
                        ]
                    ],
                    500
                );
            }
        }

        $order->order_status_id = $to->id;

//        status update will sync the data
        if (!$request->has('not_sync')) {
            $order->is_pos_sync = 1;
        }

        $order->save();
        $order->refresh();

        return $order;
    }

    /**
     * @param $result
     * @return OrderPosCollection
     */
    protected function returnOrderPosCollectionOrOther($result)
    {
        return ($result instanceof Order)
            ? new OrderPosCollection($result)
            : $result;
    }


    public function getStoreStatus($shop_id, $source_code){
        try {
            $shop = Shop::find($shop_id);
            $orderSourceType = OrderSourceType::where('code', $source_code)->first();
            $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
            try {
                $client = $orderSource->orderSourceType->client_class::make()
                    ->initClient($orderSource, $shop);
            }
            catch (\Exception $exception){
                return response()->json(
                    [
                        'status' => 'ERROR',
                        'offlineReason' => 'CREDENTIAL_MISMATCH',
                        'message' => 'Credential Doesnt match',
                        'errors' => $exception->getMessage()
                    ],
                    200
                );
            }
            $status = $client->getStoreStatus($orderSource);
            return response($status);
        }
        catch (OrderSourceClientException $exception) {
            return response()->json(
                [
                    'errors' => $exception->getMessage()
                ],
                500
            );
        }
    }


    public function setStoreStatus(Request $request){
        try {
            $data = $request->all();
            $shop_id = $data['shop_id'];
            $source_code = $data['source_code'];
            $status = $data['status'];
            $shop = Shop::find($shop_id);
            $orderSourceType = OrderSourceType::where('code', $source_code)->first();
            $orderSource = OrderSourceShop::where('shop_id', $shop_id)->where('order_source_type_id', $orderSourceType->id)->first();
            $client = $orderSource->orderSourceType->client_class::make()
                ->initClient($orderSource, $shop);
            $status = $client->setStoreStatus($orderSource, $status);
            return response($status);
        }
        catch (OrderSourceClientException $exception) {
            return response()->json(
                [
                    'errors' => $exception->getMessage()
                ],
                500
            );
        }
    }

}
