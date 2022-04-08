<?php

namespace Tests\Feature;

use App\Models\OrderSourceType;

trait OrderStatusTestTrait
{
    public function createAndTestOrderStatus(OrderSourceType $sourceType)
    {
        $orderStatus = OrderStatusTest::$orderStatus;
        $orderStatus['source_type_id'] = $sourceType->id;

        $post = $this->post('/api/v1/order-statuses', $orderStatus);

        $post->assertStatus(201);

        $created = $post->json();

        $this->assertEquals($orderStatus['code'], $created['data']['code']);
        $this->assertEquals($orderStatus['name'], $created['data']['name']);
        $this->assertEquals($orderStatus['source_type_id'], $created['data']['source_type']['id']);
        $this->assertNull($created['data']['parent_id']);
        $this->assertEquals($orderStatus['is_active'], $created['data']['is_active']);

        return $created;
    }
}
