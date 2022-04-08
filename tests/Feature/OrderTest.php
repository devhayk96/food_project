<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
//    use RefreshDatabase, OrderStatusTestTrait, OrderSourceTestTrait, OrderTypeTestTrait, ShopTestTrait;
    use RefreshDatabase, OrderStatusTestTrait, OrderSourceTestTrait, OrderTypeTestTrait;

    public static array $order = [
    ];

    public function testCreateAndListOrdersWorksAsExpectedWithCorrectData()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

//        $orderStatus = $this->createAndTestOrderStatus();
//        $orderType = $this->createAndTestOrderType();
//        $shop = $this->createAndTestShop();

    }
}
