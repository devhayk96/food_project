<?php

namespace Database\Seeders;

use App\Models\PermissionObject;
use Illuminate\Database\Seeder;

class PermissionObjectsSeeder extends Seeder {

    public function run()
    {
        $data = [
            [
                'name' => 'orders',
                'display_name' => 'Orders',
            ], [
                'name' => 'shops',
                'display_name' => 'Shops',
            ], [
                'name' => 'clients',
                'display_name' => 'Clients',
            ], [
                'name' => 'vats',
                'display_name' => 'VATs',
            ], [
                'name' => 'product_groups',
                'display_name' => 'Product groups',
            ], [
                'name' => 'product_subgroups',
                'display_name' => 'Product subgroups',
            ], [
                'name' => 'products',
                'display_name' => 'Products',
            ], [
                'name' => 'optional_groups',
                'display_name' => 'Optional groups',
            ], [
                'name' => 'menu_categories',
                'display_name' => 'Menu categories',
            ], [
                'name' => 'menus',
                'display_name' => 'Menus',
            ], [
                'name' => 'order_source_types',
                'display_name' => 'Order source types',
            ], [
                'name' => 'order_sources',
                'display_name' => 'Order sources',
            ], [
                'name' => 'payment_methods',
                'display_name' => 'Payment methods',
            ], [
                'name' => 'order_types',
                'display_name' => 'Order types',
            ], [
                'name' => 'order_statuses',
                'display_name' => 'Order statuses',
            ], [
                'name' => 'courier_types',
                'display_name' => 'Courier types',
            ], [
                'name' => 'kitchens',
                'display_name' => 'Kitchens',
            ], [
                'name' => 'tables',
                'display_name' => 'Tables',
            ], [
                'name' => 'cancellation_reasons',
                'display_name' => 'Cancellation reasons',
            ], [
                'name' => 'profile',
                'display_name' => 'Profile',
            ], [
                'name' => 'users',
                'display_name' => 'Users',
            ], [
                'name' => 'roles',
                'display_name' => 'Roles',
            ], [
                'name' => 'permissions',
                'display_name' => 'Permissions',
            ], [
                'name' => 'device',
                'display_name' => 'Devices',
            ]
        ];

        foreach ($data as $object) {
            $existedObject = PermissionObject::where('name', $object['name'])->first();
            if (!$existedObject) {
                PermissionObject::create($object);
            } else {
                $existedObject->update($object);
            }
        }

        PermissionObject::whereNotIn('name', array_column($data, 'name'))->delete();
    }
}
