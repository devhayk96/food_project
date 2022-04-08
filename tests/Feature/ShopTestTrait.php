<?php

namespace Tests\Feature;

trait ShopTestTrait
{
    use AddressTestTrait, OrderSourceTestTrait;

    protected function createAndTestShop()
    {
        $orderSource = $this->createAndTestOrderSource();

        $anotherSource = [
            'name' => 'Best delivery website for Netherlands market',
            'code' => 'another-source',
            'is_active' => false,
            'credentials' => [
                'username' => 'omed',
                'password' => 'demo5678',
                'apiKey' => 'trueKey'
            ]
        ];
        $anotherSource['order_source_type_id'] = $orderSource['data']['order_source_type']['id'];

        $anotherSourceResponse = $this->post('/api/v1/order-sources', $anotherSource);
        $anotherSourceResponse->assertStatus(201);
        $anotherSourceResult = $anotherSourceResponse->json();
        $this->assertEquals($anotherSource['name'], $anotherSourceResult['data']['name']);
        $this->assertEquals($anotherSource['credentials'], $anotherSourceResult['data']['credentials']);

        $address = $this->createAndTestAddress();

        $shop = ShopTest::$shop;
        $shop['address_id'] = $address['data']['id'];
        $shop['order_sources'] = [
            ['id' => $orderSource['data']['id']],
            ['id' => $anotherSourceResult['data']['id']]
        ];

        $response = $this->post('/api/v1/shops', $shop);
        $created = $response->json();

        $this->assertEquals($shop['name'], $created['data']['name']);
        $this->assertEquals($shop['description'], $created['data']['description']);
        $this->assertEquals($shop['address_id'], $created['data']['address']['id']);
        $this->assertEquals($shop['thuisbezorgd_restaurant_id'], $created['data']['thuisbezorgd_restaurant_id']);
        $this->assertEquals($orderSource['data']['id'], $created['data']['order_sources'][0]['id']);

        return $created;
    }
}
