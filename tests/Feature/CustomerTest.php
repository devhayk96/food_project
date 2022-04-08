<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected static array $customer = [
        'name' => 'Riccardo De Leo',
        'company' => 'palms.nl',
        'phone' => '0044 07734 473589',
        'email' => 'riccardo@plams.nl',
        'note' => 'He\'s the best developer ever! super duper computer ++'
    ];

    public function testCreateAndListCustomersWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $post = $this->post('/api/v1/customers', self::$customer);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(self::$customer['name'], $created['data']['name']);
        $this->assertEquals(self::$customer['company'], $created['data']['company']);
        $this->assertEquals(self::$customer['phone'], $created['data']['phone']);
        $this->assertEquals(self::$customer['email'], $created['data']['email']);
        $this->assertEquals(self::$customer['note'], $created['data']['note']);

        $get = $this->get('/api/v1/customers');

        $get->assertStatus(200);

        $list = $get->json();

        $this->assertEquals(self::$customer['name'], $list['data'][0]['name']);
        $this->assertEquals(self::$customer['company'], $list['data'][0]['company']);
        $this->assertEquals(self::$customer['phone'], $list['data'][0]['phone']);
        $this->assertEquals(self::$customer['email'], $list['data'][0]['email']);
        $this->assertEquals(self::$customer['note'], $list['data'][0]['note']);
    }
}
