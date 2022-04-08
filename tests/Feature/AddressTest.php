<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase, AddressTestTrait;

    public static array $address = [
        'street' => 'Via S Jachiddu, 25',
        'postcode' => '98125',
        'city' => 'Messina',
        'extra' => 'Complesso La Perla'
    ];

    public function testCreateAndListAddressesWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $this->artisan('db:seed');

        $address = $this->createAndTestAddress();
        $get = $this->get('/api/v1/addresses');

        $get->assertStatus(200);

        $list = $get->json();

        $this->assertEquals($address['data']['street'], $list['data'][1]['street']);
        $this->assertEquals($address['data']['postcode'], $list['data'][1]['postcode']);
        $this->assertEquals($address['data']['city'], $list['data'][1]['city']);
        $this->assertEquals($address['data']['extra'], $list['data'][1]['extra']);
    }
}
