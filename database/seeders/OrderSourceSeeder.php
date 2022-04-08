<?php

namespace Database\Seeders;

use App\Entities\FoodyxCredentials;
use App\Entities\ThuisbezorgdCredentials;
use App\Entities\UberEatsCredentials;
use App\Models\OrderSourceShop;
use App\Models\OrderSourceType;

class OrderSourceSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = $this->getSystemUser();
        $foodyx = OrderSourceType::where('code', OrderSourceType::FOODYX)->firstOrFail();
        $foodyxCredentials = FoodyxCredentials::make(['token' => '']);
        $foodyxTest = OrderSourceShop::create([
            'order_source_type_id' => $foodyx->id,
            'shop_id' => 1,
            'code' => 'foodyx-test',
            'name' => 'Foodyx Test Account',
            'credentials' => $foodyxCredentials->serialize(),
            'is_active' => true,
            'is_auto_accept' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $thuisbezorgd = OrderSourceType::where('code', OrderSourceType::THUISBEZORGD)->firstOrFail();
        $thuisbezorgdCredentials = ThuisbezorgdCredentials::make([
            'restaurantId' => 'pos4pizza',
            'username' => 'pos4',
            'password' => 'test',
            'apiKey' => 'fdsffjef87efja3au0a032j4fa9u4j8fgaw3324irkfa0irk490af'
        ]);
        $thuisbezorgdTest = OrderSourceShop::create([
            'order_source_type_id' => $thuisbezorgd->id,
            'shop_id' => 1,
            'code' => 'thuisbezorgd-pos4-test',
            'name' => 'Thuisbezorgd Pos4 Test Account',
            'credentials' => $thuisbezorgdCredentials->serialize(),
            'is_active' => true,
            'is_auto_accept' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);

        $ubereats = OrderSourceType::where('code', OrderSourceType::UBEREATS)->firstOrFail();
        $ubereatsCredentials = UberEatsCredentials::make([
            'restaurantId' => '01f9c531-c118-4539-a254-99eb6ee02c5e',
            'clientId' => '5fqZZw7b7mcgLpfosiUVYpc3qbVJpQBC',
            'clientSecret' => 'WG9jYIyptrDzSEet5hA90rEo89UQwShj-RHyly8c'
        ]);
        $ubereatsTest = OrderSourceShop::create([
            'order_source_type_id' => $ubereats->id,
            'shop_id' => 1,
            'code' => 'ubereats-pos4-test',
            'name' => 'Uber Eats Pos4 Test Account',
            'credentials' => $ubereatsCredentials->serialize(),
            'is_active' => true,
            'is_auto_accept' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
    }
}
