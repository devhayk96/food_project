<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public static array $group = [
        'code' => 'sevenhalf',
        'name' => 'seven and a half',
        'description' => 'This is a group description, it can be very long.'
    ];

    public function testCreateAndListGroupsWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $post = $this->post('/api/v1/groups', self::$group);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(self::$group['code'], $created['data']['code']);
        $this->assertEquals(self::$group['name'], $created['data']['name']);
        $this->assertEquals(self::$group['description'], $created['data']['description']);

        $get = $this->get('/api/v1/groups');

        $get->assertStatus(200);

        $list = $get->json();

        $this->assertEquals(self::$group['code'], $list['data'][0]['code']);
        $this->assertEquals(self::$group['name'], $list['data'][0]['name']);
        $this->assertEquals(self::$group['description'], $list['data'][0]['description']);
    }
}
