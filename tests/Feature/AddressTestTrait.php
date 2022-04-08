<?php

namespace Tests\Feature;

trait AddressTestTrait
{
    protected function createAndTestAddress()
    {
        $post = $this->post('/api/v1/addresses', AddressTest::$address);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(AddressTest::$address['street'], $created['data']['street']);
        $this->assertEquals(AddressTest::$address['postcode'], $created['data']['postcode']);
        $this->assertEquals(AddressTest::$address['city'], $created['data']['city']);
        $this->assertEquals(AddressTest::$address['extra'], $created['data']['extra']);

        return $created;
    }
}
