<?php

namespace Database\Seeders;

use App\Clients\UberEatsClient;
use App\Entities\UberEatsWallet;
use App\Models\Address;
use App\Models\OrderSource;
use App\Models\Shop;

class ShopSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = $this->getSystemUser();
        /*$foodyx = OrderSource::where('code', '=', 'foodyx-test')
            ->firstOrFail();
        $thuisbezorgd = OrderSource::where('code', '=', 'thuisbezorgd-pos4-test')
            ->firstOrFail();
        $ubereats = OrderSource::where('code', '=', 'ubereats-pos4-test')
            ->firstOrFail();*/
        $address = Address::where('street', '=','Wilhelminakade 909 3072 AP')
            ->firstOrFail();
        $shop = Shop::create([
            'name' => 'pos4 API Testshop',
            'delivery_time' => 45,
            'pickup_time' => 25,
            'address' => json_encode($address),
            'is_open' => true,
            'is_delivering' => true,
            'is_active' => true,
            'is_visible' => true,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id,
        ]);
//        $shop->orderSources()->attach([$thuisbezorgd->id, $ubereats->id, $foodyx->id]);

        /**
         * @var UberEatsClient $uberEatsClient
         */
        /*$uberEatsClient = UberEatsClient::make()->initClient($ubereats, $shop);
        foreach (UberEatsWallet::$scopes as $scope) {
            echo "\r\n Generating scope " . $scope . "\r\n";
            $uberEatsClient->generateToken($scope);
        }

        $ubereats->credentials = $uberEatsClient->credentials->serialize();
        $ubereats->save();*/
    }
}
