<?php

namespace App\Http\Resources;

use App\Models\Delivery;
use App\Models\OrderSourceShop;
use Illuminate\Http\Request;

class OrderCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->decorateWithId([
//            'order_no' => $this->order_no,
            'daily_order_number' => isset($this->daily_order_number)?$this->daily_order_number: null,
            'order_number' => isset($this->order_number)?$this->order_number: null,
            'order_status' => new SourceTypeBasedHierarchicalRelationshipCollection($this->orderStatus),
            'order_source' => new OrderSourceCollection($this->orderSource),
            'order_type' => new SourceTypeBasedHierarchicalRelationshipCollection($this->orderType),
            'shop' => $this->shop,
            'customer' => new CustomerCollection($this->customer),
            'order_products' => OrderProductResource::collection($this->orderProducts),
//            'delivery' => new DeliveryCollection(json_decode($this->address_json)),
            'address' => new AddressCollection(json_decode($this->address_json)),
        //    'delivery_cost' => $this->locale->formatPriceWithCurrency($this->delivery_cost),
            'delivery_cost' => (float)$this->delivery_cost,
            'delivery_remarks' => $this->delivery_remarks,
            'payment_method' => new SourceTypeBasedHierarchicalRelationshipCollection($this->paymentMethod),
            'is_asap' => $this->is_asap,
            'requested_time' => empty($this->requested_time)
                ? $this->requested_time
                : $this->locale->getStringFromSystemToLocaleTzToDtfLocale($this->requested_time),
            'actual_delivery_time' => empty($this->actual_delivery_time)
                ? $this->actual_delivery_time
                : $this->locale->getStringFromSystemToLocaleTzToDtfLocale($this->actual_delivery_time),
            'order_date' => $this->locale->getStringFromSystemToLocaleTzToDtfDateOnly($this->order_datetime),
            'order_time' => $this->locale->getStringFromSystemToLocaleTzToDtfTimeOnly($this->order_datetime),
            'total_discount' => $this->total_discount,
            'total_price' =>  $this->total_price,
            'tip_price' =>  (float)$this->tip_price,
            /*'total_discount' => $this->locale->formatPriceWithCurrency((float)$this->total_discount),
            'total_price' =>  $this->locale->formatPriceWithCurrency((float)$this->total_price),            
            'tip_price' =>  $this->locale->formatPriceWithCurrency((float)$this->tip_price),*/
            'sub_total' => $this->total_price - $this->delivery_cost,
            'is_paid' => $this->is_paid,
            'order_remark' => $this->order_remark,
            'order_json' => $this->order_json,

            'address_id' => isset($this->address_id)?$this->address_id:null,
            'extra_cost_vat_id' => isset($this->extra_cost_vat_id)?$this->extra_cost_vat_id:null,
            'delivery_vat_id' => isset($this->delivery_vat_id)?$this->delivery_vat_id:null,
            'delivery_vat_amount' => isset($this->delivery_vat_amount)?$this->delivery_vat_amount:null,
            'table_id' => isset($this->table_id)?$this->table_id:null,
            'cancellation_id' => isset($this->cancellation_id)?$this->cancellation_id:null,
            'payment_session_id' => isset($this->payment_session_id)?$this->payment_session_id:null,
            'cancellation_comment' => isset($this->cancellation_comment)?$this->cancellation_comment:null,
            // 'extra_cost_vat_amount' => isset($this->extra_cost_vat_amount)?$this->locale->formatPriceWithCurrency((float)$this->extra_cost_vat_amount):null,
            // 'extra_costs' => isset($this->extra_costs)?$this->locale->formatPriceWithCurrency((float)$this->extra_costs):null,
            'extra_cost_vat_amount' => isset($this->extra_cost_vat_amount)?$this->extra_cost_vat_amount:null,
            'extra_costs' => isset($this->extra_costs)?$this->extra_costs:null,
            'is_printed' => isset($this->is_printed)?$this->is_printed:null,
            'is_pos_sync' => isset($this->is_pos_sync)?$this->is_pos_sync:null,
//            'pays_with' => isset($this->pays_with)?$this->locale->formatPriceWithCurrency((float)$this->pays_with):null,
            'pays_with' => isset($this->pays_with)?$this->pays_with:null,
            'number_of_guests' => isset($this->number_of_guests)?$this->number_of_guests:null,
            'course' => isset($this->course)?$this->course:null,
            'distance' => isset($this->distance)?$this->distance:null,
            'invoice_id' => isset($this->invoice_id)?$this->invoice_id:null,
            'courier_left_at' => isset($this->courier_left_at)?$this->locale->getStringFromSystemToLocaleTzToDtfLocale($this->courier_left_at):null,
            'courier_back_at' => isset($this->courier_back_at)?$this->locale->getStringFromSystemToLocaleTzToDtfLocale($this->courier_back_at):null,

            'kitchen_id' => isset($this->kitchen_id)?$this->kitchen_id:null,
            'courier_id' => isset($this->courier_id)?$this->courier_id:null,
            'is_discount_percentage' => isset($this->is_discount_percentage)?$this->is_discount_percentage:null,

//            'requested_products' => unserialize($this->requested_products),
//            'requested_discounts' => unserialize($this->requested_discounts),
//            'original_json' => unserialize($this->original_json),
            /*'thuisbezorgd' => [
                'id' => $this->thuisbezorgd_id,
                'order_key' => $this->thuisbezorgd_order_key,
                'platform' => $this->thuisbezorgd_platform,
                'public_reference' => $this->thuisbezorgd_public_reference,
                'pays_with' => (empty($this->thuisbezorgd_pays_with))
                    ? null
                    : $this->locale->formatPrice($this->thuisbezorgd_pays_with)
            ],
            'ubereats' => [
                'id' => $this->ubereats_id
            ]*/
        ]);
    }
}
