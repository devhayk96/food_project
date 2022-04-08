<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class ShopTest extends TestCase
{
    use RefreshDatabase,ShopTestTrait;

    public static array $shop = [
        'name' => 'Peppe pizza shop',
        'description' => 'The best pizza of whole Naples.',
        'address_id' => 1,
        'thuisbezorgd_restaurant_id' => '1234',
        'is_open' => true
    ];

    public function testCreateAndListShopsWorksAsExpectedWithCorrectData()
    {
        $this->artisan('db:seed');

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $shop = $this->createAndTestShop();

        $anotherShopData = [
            'name' => 'Riccardo Arancino shop',
            'description' => 'The best arancino of whole Sicily.',
            'address_id' => $shop['data']['address']['id'],
            'thuisbezorgd_restaurant_id' => '1234567',
            'is_open' => true
        ];

        $anotherShopData['order_sources'] = [
            ['id' => $shop['data']['order_sources'][1]['id']]
        ];

        $anotherResponse = $this->post('/api/v1/shops', $anotherShopData);
        $anotherShop = $anotherResponse->json();

        $this->assertEquals($anotherShopData['name'], $anotherShop['data']['name']);
        $this->assertEquals($anotherShopData['description'], $anotherShop['data']['description']);
        $this->assertEquals($anotherShopData['address_id'], $anotherShop['data']['address']['id']);
        $this->assertEquals(
            $anotherShopData['thuisbezorgd_restaurant_id'],
            $anotherShop['data']['thuisbezorgd_restaurant_id']
        );
        $this->assertEquals($shop['data']['order_sources'][1]['id'], $anotherShop['data']['order_sources'][0]['id']);
    }
}
