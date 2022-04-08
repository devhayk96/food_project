<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    public static array $discount = [
        'code' => 'sevenhalf',
        'name' => 'seven and a half',
        'value' => 7.5,
        'is_active' => false
    ];

    public function testCreateAndListAddressesWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $post = $this->post('/api/v1/discounts', self::$discount);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(self::$discount['code'], $created['data']['code']);
        $this->assertEquals(self::$discount['name'], $created['data']['name']);
        $this->assertEquals(self::$discount['value'], $created['data']['value']);
        $this->assertEquals(self::$discount['is_active'], $created['data']['is_active']);

        $get = $this->get('/api/v1/discounts');

        $get->assertStatus(200);

        $list = $get->json();

        $this->assertEquals(self::$discount['code'], $list['data'][0]['code']);
        $this->assertEquals(self::$discount['name'], $list['data'][0]['name']);
        $this->assertEquals(self::$discount['value'], $list['data'][0]['value']);
        $this->assertEquals(self::$discount['is_active'], $list['data'][0]['is_active']);
    }
}
