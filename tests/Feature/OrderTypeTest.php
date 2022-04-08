<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class OrderTypeTest extends TestCase
{
    use RefreshDatabase, OrderTypeTestTrait;

    public static array $orderType = [
        'code' => 'restaurant',
        'name' => 'This is an order type that at the moment doesn\'n exists',
        'is_active' => true
    ];

    public function testCreateAndListOrderTypesWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $pos = OrderSourceType::where(['code' => 'pos'])->firstOrFail();

        $orderType = $this->createAndTestOrderType($pos);

//        $listResponse = $this->get('/api/v1/order-types');
//
//        $listResponse->assertStatus(200);

//        $list = $listResponse->json();
//
//        $this->assertEquals($orderType['data']['code'], $list['data'][0]['code']);
//        $this->assertEquals($orderType['data']['description'], $list['data'][0]['description']);
//        $this->assertEquals($orderType['data']['thuisbezorgd_code'], $list['data'][0]['thuisbezorgd_code']);
    }
}
