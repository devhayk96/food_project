<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourierTypeTest extends TestCase
{
    use RefreshDatabase;

    public static array $courierType = [
        'code' => 'internal-test',
        'name' => 'Test',
        'is_active' => true
    ];
    public function testCreateAndListCourierTypeWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $pos = OrderSourceType::where(['code' => 'pos'])->firstOrFail();

        $courierType = self::$courierType;
        $courierType['source_type_id'] = $pos->id;


        $post = $this->post('/api/v1/courier-types', $courierType);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals($courierType['code'], $created['data']['code']);
        $this->assertEquals($courierType['name'], $created['data']['name']);
        $this->assertEquals($courierType['source_type_id'], $created['data']['source_type']['id']);
        $this->assertNull($created['data']['parent_id']);
        $this->assertEquals($courierType['is_active'], $created['data']['is_active']);

//        $listResponse = $this->get('/api/v1/payment-methods/');
//
//        $listResponse->assertStatus(200);
//
//        $list = $listResponse->json();
//
//        $latest = end($list['data']);
//
//        $this->assertEquals(self::$paymentMethod['code'], $latest['code']);
//        $this->assertEquals(self::$paymentMethod['name'], $latest['name']);
//        $this->assertEquals(self::$paymentMethod['description'], $latest['description']);
//        $this->assertEquals(self::$paymentMethod['thuisbezorgd_code'], $latest['thuisbezorgd_code']);
//        $this->assertEquals(self::$paymentMethod['is_active'], $latest['is_active']);
    }


}
