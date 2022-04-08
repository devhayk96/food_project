<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\AddressCollection;
use App\Http\Resources\Pos\AddressPosCollection;
use App\Http\Resources\OrderProductResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Resources\EditHistoryResource;
use DateTime;
use DateTimeZone;

class OrderPosCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
            $requested_time_value = $this->requested_time ;
			$order_datetime_value = $this->order_datetime;

        // if ($this->orderSource->orderSourceType->code == 'website') {
        //     $src_dt = $this->order_datetime;
        //     $src_tz =  new DateTimeZone('UTC');
        //     $dest_tz = new DateTimeZone('Europe/Amsterdam');
        //     $dt = new DateTime($src_dt, $src_tz);
        //     $dt->setTimeZone($dest_tz);

        //     $dest_dt = $dt->format('Y-m-d H:i:s');
        //     $order_datetime_value = $dest_dt;
        //     if (isset($this->requested_time)) {

        //         $src_dt = $this->requested_time;
        //         $src_tz =  new DateTimeZone('UTC');
        //         $dest_tz = new DateTimeZone('Europe/Amsterdam');
        //         $dt = new DateTime($src_dt, $src_tz);
        //         $dt->setTimeZone($dest_tz);

        //         $dest_dt = $dt->format('Y-m-d H:i:s');

        //         $requested_time_value = $dest_dt;
            
        //     }
        // }

        return [
            'order_id' => $this->id,
            'daily_order_number' => $this->daily_order_number,
            'order_number' => $this->order_number,
            'order_status_id' => $this->orderStatus->code,
            'order_source_id' => $this->orderSource->orderSourceType->code,
            'order_type_id' => $this->orderType->code,
            'shop_id' => $this->shop->id,
            'courier_type_id' => isset($this->courierType) ? $this->courierType->code : null,
            'table_id' => $this->table_id,
            'cancellation_id' => isset($this->cancellationReason) ? $this->cancellationReason->code : null,
            'cancellation_comment' => $this->cancellation_comment,
            'delivery_cost' => (float)$this->delivery_cost,
            'delivery_vat_id' => isset($this->deliveryVat) ? $this->deliveryVat->code : null,
            'delivery_vat_amount' => $this->delivery_vat_amount,
            'delivery_remarks' => $this->delivery_remarks,
            'payment_method_id' => $this->paymentMethod->code,
            'payment_session_id' => $this->payment_session_id,
            'is_asap' => $this->is_asap,
            'requested_time' => $requested_time_value,
            'actual_delivery_time' => isset($this->actual_delivery_time) ? $this->actual_delivery_time : null,
            'order_datetime' => $order_datetime_value,
            'total_discount' => $this->total_discount,
            'total_price' => $this->total_price,
            'pays_with' => isset($this->pays_with) ? $this->pays_with : null,
            'extra_costs' => isset($this->extra_costs) ? $this->extra_costs : null,
            'is_printed' => isset($this->is_printed) ? $this->is_printed : null,
            'total_vat_amount' => isset($this->total_vat_amount) ? $this->total_vat_amount : null,
            'extra_cost_vat_id' => isset($this->extraCostVat) ? $this->extraCostVat->code : null,
            'extra_cost_vat_amount' => isset($this->extra_cost_vat_amount) ? $this->extra_cost_vat_amount : null,
            'tip_amount' =>  isset($this->tip_price) ? (float)$this->tip_price : null,
            'is_paid' => $this->is_paid,
            'order_remark' => $this->order_remark,
            'number_of_guests' => isset($this->number_of_guests) ? $this->number_of_guests : null,
            'course' => isset($this->course) ? $this->course : null,
            'fulfillment_issues' => isset($this->fulfillment_issues) ? json_decode($this->fulfillment_issues) : null,
            'distance' => isset($this->distance) ? $this->distance : null,
            'invoice_id' => isset($this->invoice_id) ? $this->invoice_id : null,
            'courier_left_at' => isset($this->courier_left_at) ? $this->courier_left_at : null,
            'courier_back_at' => isset($this->courier_back_at) ? $this->courier_back_at : null,
            'kitchen_id' => isset($this->kitchen_id) ? $this->kitchen_id : null,
            'courier_id' => isset($this->courier_id) ? $this->courier_id : null,
            'is_discount_percentage' => isset($this->is_discount_percentage) ? $this->is_discount_percentage : null,

            //            'is_pos_sync' => isset($this->is_pos_sync)?$this->is_pos_sync:null,

            'customer' => array_merge((array)json_decode(json_encode(new CustomerPosCollection($this->customer))), (array)json_decode(json_encode(new AddressPosCollection(json_decode($this->address_json))))),
            'products' => OrderProductPosCollection::collection($this->orderProducts),
            'discounts' => isset($this->discounts) ? OrderDiscountPosCollection::collection($this->discounts) : [],
        ];
    }
}
