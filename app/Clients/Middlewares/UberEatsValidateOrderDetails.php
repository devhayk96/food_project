<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use Illuminate\Validation\Rule;

class UberEatsValidateOrderDetails extends AbstractOrderMiddleware
{
    public function __construct()
    {
        $this->stepNumber = 4;
        $this->stepName = 'Validate order details';
        $this->logChannel = 'ubereats';

        parent::__construct();
    }

    public function process(OrderInTransit $orderInTransit): OrderInTransit
    {
        $this->validateOrder($orderInTransit, [
            'id' => 'required|string|min:1|max:256',
            'display_id' => 'required|string|min:1|max:10',
            'external_reference_id' => 'nullable|sometimes|string|min:1|max:256',
            'current_state' => [
                'required',
                'exists:poshub_order_statuses,code',
                Rule::in(['CREATED', 'ACCEPTED', 'DENIED', 'FINISHED', 'CANCELED'])
            ],
            'type' => [
                'required',
                'exists:poshub_order_types,code',
                Rule::in(['PICK_UP', 'DINE_IN', 'DELIVERY_BY_UBER', 'DELIVERY_BY_RESTAURANT'])
            ],

            'store.id' => 'required|string|min:1|max:256',
            'store.name' => 'required|string|min:1|max:256',
            'store.external_reference_id' => 'nullable|sometimes|string|min:1|max:256',

            'eater.first_name' => 'required|string|min:1|max:256',
            'eater.last_name' => 'nullable|sometimes|string|min:1|max:256',
            'eater.phone' => 'nullable|sometimes|string|min:1|max:256',
            'eater.phone_code' => 'nullable|sometimes|string|min:1|max:256',

            'cart.items.*.id' => 'required|string|min:1|max:256',
            'cart.items.*.instance_id' => 'required|string|min:1|max:256',
            'cart.items.*.title' => 'required|string|min:1|max:256',
            'cart.items.*.external_data' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.quantity' => 'required|numeric',

            'cart.items.*.price.unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.price.unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.price.unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.price.total_price.amount' => 'required|numeric',
            'cart.items.*.price.total_price.currency_code' => 'required|string|min:1|max:32',
            'cart.items.*.price.total_price.formatted_amount' => 'required|string|min:1|max:32',
            'cart.items.*.price.base_unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.price.base_unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.price.base_unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.price.base_total_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.price.base_total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.price.base_total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',

            'cart.items.*.selected_modifier_groups.*.id' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.title' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.external_data' => 'nullable|sometimes|string|min:1|max:256',

            'cart.items.*.selected_modifier_groups.*.selected_items.*.id' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.title' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.external_data' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.quantity' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.total_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_total_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.price.base_total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.selected_items.*.default_quantity' => 'nullable|sometimes|numeric',

            'cart.items.*.selected_modifier_groups.*.removed_items.*.id' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.title' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.external_data' => 'nullable|sometimes|string|min:1|max:256',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.quantity' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.total_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_total_price.amount' => 'nullable|sometimes|numeric',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.price.base_total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.items.*.selected_modifier_groups.*.removed_items.*.default_quantity' => 'nullable|sometimes|numeric',

            'cart.items.*.special_requests.*.allergy.allergens_to_exclude.*.type' => [
                'nullable',
                'sometimes',
                Rule::in(['DAIRY', 'EGGS', 'FISH', 'SHELLFISH', 'TREENUTS', 'PEANUTS', 'GLUTEN', 'SOY', 'OTHER'])
            ],
            'cart.items.*.special_requests.*.allergy.allergens_to_exclude.*.freeform_text' => 'nullable|sometimes|string|min:1|max:2048',
            'cart.items.*.special_requests.*.allergy.allergy_instructions' => 'nullable|sometimes|string|min:1|max:2048',

            'cart.items.*.default_quantity' => 'nullable|sometimes|numeric',
            'cart.items.*.special_instructions' => 'nullable|sometimes|string|min:1|max:2048',
            'cart.items.*.fulfillment_action.fulfillment_action_type' => [
                'nullable',
                'sometimes',
                Rule::in(['REPLACE_FOR_ME', 'SUBSTITUTE_ME', 'CANCEL', 'REMOVE_ITEM'])
            ],

            'cart.special_instructions' => 'nullable|sometimes|string|min:1|max:2048',

            'cart.fulfillment_issues.fulfillment_issue_type' => [
                'nullable',
                'sometimes',
                Rule::in(['OUT_OF_ITEM', 'PARTIAL_AVAILABILITY'])
            ],
            'cart.fulfillment_issues.fulfillment_action_type' => [
                'nullable',
                'sometimes',
                Rule::in(['REPLACE_FOR_ME', 'SUBSTITUTE_ME', 'CANCEL', 'REMOVE_ITEM'])
            ],

            'cart.fulfillment_issues.root_item.id' => 'nullable|sometimes|string|min:1|max:256',
            'cart.fulfillment_issues.root_item.title' => 'nullable|sometimes|string|min:1|max:256',
            'cart.fulfillment_issues.root_item.external_data' => 'nullable|sometimes|string|min:1|max:256',
            'cart.fulfillment_issues.root_item.quantity' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.root_item.price.unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.root_item.price.unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.total_price.amount' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.root_item.price.total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.base_unit_price.amount' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.root_item.price.base_unit_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.base_unit_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.base_total_price.amount' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.root_item.price.base_total_price.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.price.base_total_price.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',
            'cart.fulfillment_issues.root_item.default_quantity' => 'nullable|sometimes|numeric',

            'cart.fulfillment_issues.item_availability_info.items_requested' => 'nullable|sometimes|numeric',
            'cart.fulfillment_issues.item_availability_info.items_available' => 'nullable|sometimes|numeric',

            'payment.charges.total.amount' => 'nullable|sometimes|numeric',
            'payment.charges.total.currency_code' => 'nullable|sometimes|string|min:1|max:32',
            'payment.charges.total.formatted_amount' => 'nullable|sometimes|string|min:1|max:32',

            'placed_at' => 'required|date_format:Y-m-d\TH:i:s\Z',
            'estimated_ready_for_pickup_at' => 'required|date_format:Y-m-d\TH:i:s\Z'
        ]);

        return $orderInTransit;
    }
}
