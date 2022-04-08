<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;

trait OrderTypeTestTrait
{
    public function createAndTestOrderType(OrderSourceType $sourceType)
    {
        $orderType = OrderTypeTest::$orderType;
        $orderType['source_type_id'] = $sourceType->id;

        $post = $this->post('/api/v1/order-types', $orderType);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals($orderType['code'], $created['data']['code']);
        $this->assertEquals($orderType['name'], $created['data']['name']);
        $this->assertEquals($orderType['source_type_id'], $created['data']['source_type']['id']);
        $this->assertNull($created['data']['parent_id']);
        $this->assertEquals($orderType['is_active'], $created['data']['is_active']);
        return $created;
    }
}
