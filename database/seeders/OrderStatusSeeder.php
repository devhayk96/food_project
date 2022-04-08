<?php

namespace Database\Seeders;

use App\Models\OrderSourceType;
use App\Models\OrderStatus;

class OrderStatusSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = $this->getSystemUser();

        $pos = OrderSourceType::where(['code' => 'pos'])->firstOrFail();
        $thuisbezorgd = OrderSourceType::where(['code' => 'thuisbezorgd'])->firstOrFail();
        $ubereats = OrderSourceType::where(['code' => 'ubereats'])->firstOrFail();

        $new = OrderStatus::create([
            'code' => OrderStatus::NEW,
            'name' => 'New',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $declined = OrderStatus::create([
            'code' => OrderStatus::DECLINED,
            'name' => 'Declined',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $accepted = OrderStatus::create([
            'code' => OrderStatus::ACCEPTED,
            'name' => 'Accepted',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $received = OrderStatus::create([
            'code' => OrderStatus::RECEIVED,
            'name' => 'Received',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $kitchen = OrderStatus::create([
            'code' => OrderStatus::KITCHEN,
            'name' => 'Kitchen',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $delivery = OrderStatus::create([
            'code' => OrderStatus::DELIVERY,
            'name' => 'In Delivery',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $cancelled = OrderStatus::create([
            'code' => OrderStatus::CANCELLED,
            'name' => 'Cancelled',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $finished = OrderStatus::create([
            'code' => OrderStatus::FINISHED,
            'name' => 'Finished',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $error = OrderStatus::create([
            'code' => OrderStatus::ERROR,
            'name' => 'Error',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdPrinted = OrderStatus::create([
            'code' => 'printed',
            'name' => 'Printed',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $new->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdConfirmed = OrderStatus::create([
            'code' => 'confirmed_change_delivery_time',
            'name' => 'Confirmed',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $accepted->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdKitchen = OrderStatus::create([
            'code' => 'kitchen',
            'name' => 'Kitchen',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $kitchen->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdInDelivery = OrderStatus::create([
            'code' => 'in_delivery',
            'name' => 'In Delivery',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $delivery->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdDelivered = OrderStatus::create([
            'code' => 'delivered',
            'name' => 'Delivered',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $finished->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsCreated = OrderStatus::create([
            'code' => 'created',
            'name' => 'Created',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $new->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsAccepted = OrderStatus::create([
            'code' => 'accepted',
            'name' => 'Accepted',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $accepted->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsDenied = OrderStatus::create([
            'code' => 'denied',
            'name' => 'Denied',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $declined->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsFinished = OrderStatus::create([
            'code' => 'finished',
            'name' => 'Finished',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $finished->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsCanceled = OrderStatus::create([
            'code' => 'canceled',
            'name' => 'Canceled',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $cancelled->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsArriving = OrderStatus::create([
            'code' => 'arriving',
            'name' => 'Drriving',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $delivery->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }
}
