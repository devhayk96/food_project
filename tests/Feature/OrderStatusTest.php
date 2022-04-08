<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase, OrderStatusTestTrait;

    public static array $orderStatus = [
        'code' => 'PRINTED',
        'name' => 'The order is printed',
        'is_active' => true
    ];

    public function testCreateAndListOrderStatusesWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $pos = OrderSourceType::where(['code' => 'pos'])->firstOrFail();

        $orderStatus = $this->createAndTestOrderStatus($pos);

//        $listResponse = $this->get('/api/v1/order-statuses');
//
//        $listResponse->assertStatus(200);
//
//        $list = $listResponse->json();
//
//        $this->assertEquals($orderStatus['data']['code'], $list['data'][0]['code']);
//        $this->assertEquals($orderStatus['data']['description'], $list['data'][0]['description']);
//        $this->assertEquals($orderStatus['data']['thuisbezorgd_code'], $list['data'][0]['thuisbezorgd_code']);
    }
}
