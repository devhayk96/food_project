<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'superadmin' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u,d',
            'roles' => 'c,r,u,d',
            'permissions' => 'r,u',
            'orders' => 'c,r,u,d',
            'shops' => 'c,r,u,d',
            'clients' => 'c,r,u,d',
            'vats' => 'c,r,u,d',
            'product_groups' => 'c,r,u,d',
            'product_subgroups' => 'c,r,u,d',
            'products' => 'c,r,u,d',
            'optional_groups' => 'c,r,u,d',
            'menu_categories' => 'c,r,u,d',
            'menus' => 'c,r,u,d',
            'order_source_types' => 'c,r,u,d',
            'order_sources' => 'c,r,u,d',
            'payment_methods' => 'c,r,u,d',
            'order_types' => 'c,r,u,d',
            'order_statuses' => 'c,r,u,d',
            'courier_types' => 'c,r,u,d',
            'kitchens' => 'c,r,u,d',
            'tables' => 'c,r,u,d',
            'customers' => 'c,r,u,d',
            'cancellation_reasons' => 'c,r,u,d',
        ],
        'admin' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u,d',
            'orders' => 'c,r,u,d',
            'shops' => 'c,r,u,d',
            'clients' => 'c,r,u,d',
            'vats' => 'c,r,u,d',
            'product_groups' => 'c,r,u,d',
            'product_subgroups' => 'c,r,u,d',
            'products' => 'c,r,u,d',
            'optional_groups' => 'c,r,u,d',
            'menu_categories' => 'c,r,u,d',
            'menus' => 'c,r,u,d',
            'order_source_types' => 'c,r,u,d',
            'order_sources' => 'c,r,u,d',
            'payment_methods' => 'c,r,u,d',
            'order_types' => 'c,r,u,d',
            'order_statuses' => 'c,r,u,d',
            'courier_types' => 'c,r,u,d',
            'kitchens' => 'c,r,u,d',
            'tables' => 'c,r,u,d',
            'customers' => 'c,r,u,d',
            'cancellation_reasons' => 'c,r,u,d',
        ],
        'manager' => [
            'users' => 'c,r',
            'profile' => 'r,u,d',
            'orders' => 'r',
        ],
        'operations' => [
            'profile' => 'r,u,d',
            'orders' => 'r',
        ],
        'api-user' => [
            'profile' => 'r,u,d',
            'orders' => 'c,r,u',
            'menu' => 'r'
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
