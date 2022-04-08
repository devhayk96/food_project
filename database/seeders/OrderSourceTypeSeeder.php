<?php

namespace Database\Seeders;

use App\Models\OrderSourceType;

class OrderSourceTypeSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = $this->getSystemUser();

        OrderSourceType::create([
            'code' => OrderSourceType::FOODYX,
            'name' => 'POS',
            'credentials_class' => OrderSourceType::FOODYX_CREDENTIALS,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        OrderSourceType::create([
            'code' => OrderSourceType::THUISBEZORGD,
            'name' => 'Thuisbezorgd',
            'client_class' => 'App\Clients\ThuisbezorgdClient',
            'credentials_class' => OrderSourceType::THUISBEZORGD_CREDENTIALS,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        OrderSourceType::create([
            'code' => OrderSourceType::UBEREATS,
            'name' => 'Uber Eats',
            'client_class' => 'App\Clients\UberEatsClient',
            'credentials_class' => OrderSourceType::UBEREATS_CREDENTIALS,
            'is_active' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }
}
