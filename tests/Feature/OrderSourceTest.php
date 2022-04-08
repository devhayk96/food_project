<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class OrderSourceTest extends TestCase
{
    use RefreshDatabase, OrderSourceTestTrait;

    public static array $orderSource = [
        'code' => 'netherlands-test',
        'name' => 'Test order source from netherlands',
        'is_active' => false,
        'credentials' => [
            'username' => 'demo',
            'password' => 'demo1234',
            'apiKey' => 'falseKey'
        ]
    ];

    public function testCreateAndListOrderSourceWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $orderSource = $this->createAndTestOrderSource();

//        $listResponse = $this->get('/api/v1/order-sources');
//
//        $listResponse->assertStatus(200);
//
//        $list = $listResponse->json();
//
//        $this->assertEquals($orderSource['data']['id'], $list['data'][0]['order_source_type']['id']);
//        $this->assertEquals($orderSource['data']['order_source_type']['code'], $list['data'][0]['order_source_type']['code']);
//        $this->assertEquals($orderSource['data']['order_source_type']['name'], $list['data'][0]['order_source_type']['name']);
//        $this->assertEquals($orderSource['data']['order_source_type']['is_active'], $list['data'][0]['order_source_type']['is_active']);
//        $this->assertEquals($orderSource['data']['name'], $list['data'][0]['name']);
//        $this->assertEquals($orderSource['data']['is_active'], $list['data'][0]['is_active']);
    }
}
