<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\WorkHours;

class WorkHoursSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shop = Shop::where('name', '=', 'pos4 API Testshop')->firstOrFail();
        $user = $this->getSystemUser();

        for ($i = 0; $i <= 6; $i++) {
            WorkHours::create([
                'shop_id' => $shop->id,
                'day' => $i,
                'type' => WorkHours::TYPE_OPENING,
                'opening_hour' => '08:00',
                'closing_hour' => '24:00',
                'is_open' => true,
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id
            ]);

            WorkHours::create([
                'shop_id' => $shop->id,
                'day' => $i,
                'type' => WorkHours::TYPE_DELIVERY,
                'opening_hour' => '08:00',
                'closing_hour' => '24:00',
                'is_open' => true,
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id
            ]);
        }


    }
}
