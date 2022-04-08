<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public static array $paymentMethod = [
        'code' => 'test',
        'name' => 'Test',
        'is_active' => true
    ];

    public function testCreateAndListPaymentMethodWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $pos = OrderSourceType::where(['code' => 'pos'])->firstOrFail();

        $paymentMethod = self::$paymentMethod;
        $paymentMethod['source_type_id'] = $pos->id;


        $post = $this->post('/api/v1/payment-methods', $paymentMethod);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(self::$paymentMethod['code'], $created['data']['code']);
        $this->assertEquals(self::$paymentMethod['name'], $created['data']['name']);
        $this->assertEquals($paymentMethod['source_type_id'], $created['data']['source_type']['id']);
        $this->assertNull($created['data']['parent_id']);
        $this->assertEquals(self::$paymentMethod['is_active'], $created['data']['is_active']);

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
