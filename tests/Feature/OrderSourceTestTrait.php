<?php

namespace Tests\Feature;

trait OrderSourceTestTrait
{
    use OrderSourceTypeTestTrait;

    protected function createAndTestOrderSource()
    {
        $type = $this->createAndTestOrderSourceType();

        $orderSource = OrderSourceTest::$orderSource;
        $orderSource['order_source_type_id'] = $type['data']['id'];
        $post = $this->post('/api/v1/order-sources', $orderSource);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals($type['data']['id'], $created['data']['order_source_type']['id']);
        $this->assertEquals($type['data']['code'], $created['data']['order_source_type']['code']);
        $this->assertEquals($type['data']['name'], $created['data']['order_source_type']['name']);
        $this->assertEquals($type['data']['is_active'], $created['data']['order_source_type']['is_active']);

        $this->assertEquals(OrderSourceTest::$orderSource['code'], $created['data']['code']);
        $this->assertEquals(OrderSourceTest::$orderSource['name'], $created['data']['name']);
        $this->assertEquals(OrderSourceTest::$orderSource['is_active'], $created['data']['is_active']);
        $this->assertEquals(OrderSourceTest::$orderSource['credentials'], $created['data']['credentials']);

        return $created;
    }
}
