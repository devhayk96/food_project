<?php


namespace Tests\Feature;


trait OrderSourceTypeTestTrait
{
    protected function createAndTestOrderSourceType()
    {
        $post = $this->post('/api/v1/order-source-types', OrderSourceTypeTest::$orderSourceType);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals(OrderSourceTypeTest::$orderSourceType['code'], $created['data']['code']);
        $this->assertEquals(OrderSourceTypeTest::$orderSourceType['name'], $created['data']['name']);
        $this->assertEquals(
            OrderSourceTypeTest::$orderSourceType['client_class'],
            $created['data']['client_class']
        );
        $this->assertEquals(
            OrderSourceTypeTest::$orderSourceType['credentials_class'],
            $created['data']['credentials_class']
        );
        $this->assertEquals(OrderSourceTypeTest::$orderSourceType['is_active'], $created['data']['is_active']);

        return $created;
    }
}
