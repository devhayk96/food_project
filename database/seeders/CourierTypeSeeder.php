<?php

namespace Database\Seeders;

use App\Models\CourierType;
use App\Models\OrderSourceType;

class CourierTypeSeeder extends AbstractWithUserSeeder
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

        $posInternal = CourierType::create([
            'code' => 'internal',
            'name' => 'Internal',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $posExternal = CourierType::create([
            'code' => 'internal',
            'name' => 'Internal',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdRestaurant = CourierType::create([
            'code' => 'restaurant',
            'name' => 'Restaurant',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $posInternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdTakeaway = CourierType::create([
            'code' => 'takeaway',
            'name' => 'Takeaway',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $posExternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdExternal = CourierType::create([
            'code' => 'external',
            'name' => 'External',
            'source_type_id' => $thuisbezorgd->id,
            'is_active' => true,
            'parent_id' => $posExternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $uberEatsPickUP = CourierType::create([
            'code' => 'pick_up',
            'name' => 'Pick_ Up Uber',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $posExternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $uberEatsDineIn = CourierType::create([
            'code' => 'dine_in',
            'name' => 'Dine In Uber',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $posInternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $uberEatsDeliveryUber = CourierType::create([
            'code' => 'delivery_by_uber',
            'name' => 'Delivery By Uber',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $posExternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $uberEatsDeliveryRestaurant = CourierType::create([
            'code' => 'delivery_by_restaurant',
            'name' => 'Delivery By Restaurant In Uber',
            'source_type_id' => $ubereats->id,
            'is_active' => true,
            'parent_id' => $posExternal->id,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }
}
