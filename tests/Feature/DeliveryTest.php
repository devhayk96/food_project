<?php

namespace Tests\Feature;

use App\Models\CourierType;
use App\Models\OrderSourceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected static array $delivery = [
        'delivery_costs' => 5.54,
        'note' => 'You must delivery on time!',
    ];

    public function testCreateAndListDeliveriesWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $thuisbezorgd = OrderSourceType::where(['code' => 'thuisbezorgd'])->firstOrFail();

        $thuisbezorgdRestaurant = CourierType::where([
            'code' => 'restaurant',
            'source_type_id' => $thuisbezorgd->id
        ])->firstOrFail();
        $thuisbezorgdExternal = CourierType::where([
            'code' => 'external',
            'source_type_id' => $thuisbezorgd->id
        ])->firstOrFail();

//        $thuisbezorgd = OrderSourceType::where('code', 'thuisbezorgd')->firstOrFail();
//
//        $thuisbezorgdRestaurant = CourierType::where('code', 'restaurant')
//            ->where('source_type_id', $thuisbezorgd->id)
//            ->firstOrFail();
//
//        $thuisbezorgdExternal = CourierType::where('code', 'external')
//            ->where('source_type_id', $thuisbezorgd->id)
//            ->firstOrFail();

        $addressResponse = $this->post('/api/v1/addresses', AddressTest::$address);
        $addressResponse->assertStatus(201);

        $deliveryWithoutAddress = self::$delivery;
        $deliveryWithoutAddress['address_id'] = $addressResponse->json()['data']['id'];
        $deliveryWithoutAddress['courier_type_id'] = $thuisbezorgdRestaurant->id;

        $created = $this->post('/api/v1/deliveries', $deliveryWithoutAddress);

        $this->assertEquals($deliveryWithoutAddress['courier_type_id'], $created['data']['courier_type']['id']);
        $this->assertEquals($deliveryWithoutAddress['delivery_costs'], $created['data']['delivery_costs']);
        $this->assertEquals($deliveryWithoutAddress['note'], $created['data']['note']);

        $deliveryWithAddress = [
            'is_outsourced' => false,
            'delivery_costs' => 10,
            'note' => 'You can arrive late, enjoy the view, take a siesta.',
        ];
        $deliveryWithAddress['address_create_street'] = 'Avenida de Alemania, 59';
        $deliveryWithAddress['address_create_postcode'] = '03188';
        $deliveryWithAddress['address_create_city'] = 'Torrevieja';
        $deliveryWithAddress['address_create_extra'] = 'A very cool place';
        $deliveryWithAddress['courier_type_id'] = $thuisbezorgdExternal->id;

        $createdWithAddress = $this->post('/api/v1/deliveries', $deliveryWithAddress);

        $createdWithAddress->assertStatus(201);

        $get = $this->get('/api/v1/deliveries');

        $list = $get->json();

        $this->assertEquals($deliveryWithoutAddress['courier_type_id'], $list['data'][0]['courier_type']['id']);
        $this->assertEquals($deliveryWithoutAddress['delivery_costs'], $list['data'][0]['delivery_costs']);
        $this->assertEquals($deliveryWithoutAddress['note'], $list['data'][0]['note']);

        $this->assertEquals($deliveryWithAddress['courier_type_id'], $list['data'][1]['courier_type']['id']);
        $this->assertEquals($deliveryWithAddress['delivery_costs'], $list['data'][1]['delivery_costs']);
        $this->assertEquals($deliveryWithAddress['note'], $list['data'][1]['note']);
    }
}
