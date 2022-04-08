<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class OrderSourceTypeTest extends TestCase
{
    use RefreshDatabase, OrderSourceTypeTestTrait;

    public static array $orderSourceType = [
        'code' => 'ubereats',
        'name' => 'Uber Eats',
        'client_class' => 'App\Clients\UberEatsClient',
        'credentials_class' => 'App\Entities\UberEatsCredentials',
        'is_active' => false
     ];

    public function testCreateAndListGroupsWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $type = $this->createAndTestOrderSourceType();

        $listResponse = $this->get('/api/v1/order-source-types');

        $listResponse->assertStatus(200);

        $list = $listResponse->json();

        $this->assertEquals($type['data']['code'], $list['data'][0]['code']);
        $this->assertEquals($type['data']['name'], $list['data'][0]['name']);
        $this->assertEquals($type['data']['client_class'], $list['data'][0]['client_class']);
        $this->assertEquals($type['data']['is_active'], $list['data'][0]['is_active']);
    }
}
