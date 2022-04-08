<?php

namespace Database\Seeders;

use App\Models\OrderSourceType;
use App\Models\OrderType;

class OrderTypeSeeder extends AbstractWithUserSeeder
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

        $posDelivery = OrderType::create([
            'code' => OrderType::DELIVERY,
            'name' => 'Delivery',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $posPickup = OrderType::create([
            'code' => OrderType::PICKUP,
            'name' => 'Pickup',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdDelivery = OrderType::create([
            'code' => 'delivery',
            'name' => 'Delivery',
            'source_type_id' => $thuisbezorgd->id,
            'parent_id' => $posDelivery->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdPickup = OrderType::create([
            'code' => 'pickup',
            'name' => 'Pickup',
            'source_type_id' => $thuisbezorgd->id,
            'parent_id' => $posPickup->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsPickUp = OrderType::create([
            'code' => 'pick_up',
            'name' => 'Pickup UberEats',
            'source_type_id' => $ubereats->id,
            'parent_id' => $posPickup->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsDinIn = OrderType::create([
            'code' => 'dine_in',
            'name' => 'Dine In Uber Eats',
            'source_type_id' => $ubereats->id,
            'parent_id' => $posPickup->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsPickUp = OrderType::create([
            'code' => 'delivery_by_uber',
            'name' => 'Deivery by UberEats',
            'source_type_id' => $ubereats->id,
            'parent_id' => $posDelivery->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsDinIn = OrderType::create([
            'code' => 'delivery_by_restaurant',
            'name' => 'Delivery by Restaurant In Uber Eats',
            'source_type_id' => $ubereats->id,
            'parent_id' => $posDelivery->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

    }
}
