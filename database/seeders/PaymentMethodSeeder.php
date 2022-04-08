<?php

namespace Database\Seeders;

use App\Models\OrderSourceType;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PaymentMethodSeeder extends AbstractWithUserSeeder
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

        $posCash = PaymentMethod::create([
            'code' => 'cash',
            'name' => 'Cash',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $posIDeal = PaymentMethod::create([
            'code' => 'ideal',
            'name' => 'iDEAL',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $posPin = PaymentMethod::create([
            'code' => 'pin',
            'name' => 'Pin',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $posOnAccount = PaymentMethod::create([
            'code' => 'onaccount',
            'name' => 'On account',
            'source_type_id' => $pos->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdCash = PaymentMethod::create([
            'code' => 'cash',
            'name' => 'Cash',
            'source_type_id' => $thuisbezorgd->id,
            'parent_id' => $posCash->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdOnline = PaymentMethod::create([
            'code' => 'online',
            'name' => 'Online',
            'source_type_id' => $thuisbezorgd->id,
            'parent_id' => $posIDeal->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgdPin = PaymentMethod::create([
            'code' => 'pin_at_doorstep',
            'name' => 'Pin at doorstep',
            'source_type_id' => $thuisbezorgd->id,
            'parent_id' => $posPin->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereatsDelivery = PaymentMethod::create([
            'code' => 'delivery_by_restaurant',
            'name' => 'Uber Eats payment',
            'source_type_id' => $ubereats->id,
            'parent_id' => $posCash->id,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }
}
