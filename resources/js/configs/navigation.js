// import menuPages from './menus/pages.menu'
export default {
    // main navigation - side menu
    menu: [{
            text: 'Orders',
            key: 'menu.orders',
            permissions: 'orders-read',
            items: [{
                    icon: 'mdi-cart-outline',
                    // key: 'menu.ordersAcceptedTable',
                    text: 'New',
                    link: '/dashboard/orders/new',
                    permissions: 'orders-read'
                },
                {
                    icon: 'mdi-cart-outline',
                    // key: 'menu.ordersAcceptedTable',
                    text: 'Accepted',
                    link: '/dashboard/orders/accepted',
                    permissions: 'orders-read'
                },
                {
                    icon: 'mdi-cart-outline',
                    // key: 'menu.ordersAcceptedTable',
                    text: 'Denied',
                    link: '/dashboard/orders/denied',
                    permissions: 'orders-read'
                },
                /*{
                    icon: 'mdi-cart-outline',
                    text: 'List',
                    link: '/dashboard/orders/list'
                },*/
                /*{
                    icon: 'mdi-cart-outline',
                    key: 'menu.ordersAcceptedTable',
                    text: 'Accepted',
                    link: '/dashboard/orders/accepted'
                },*/
                {
                    icon: 'mdi-cart-off',
                    // key: 'menu.ordersErrorList',
                    text: 'Error',
                    link: '/dashboard/orders/error',
                    permissions: 'orders-read'
                },
                // {icon: 'mdi-file-outline', key: 'menu.ordersHistoryTable', text: 'History', link: '/blank'}
            ],
        },
        {
            text: 'Shop',
            key: 'menu.shop',
            permissions: 'shops-read',
            items: [{
                    icon: 'mdi-store-outline',
                    key: 'menu.shopsTable',
                    text: 'Shop List',
                    link: '/dashboard/administrations/shops',
                    permissions: 'shops-read'
                },
            ],
        },
        {
            text: 'Client',
            // key: 'menu.shop',
            permissions: 'clients-read',
            items: [{
                icon: 'mdi-store-outline',
                // key: 'menu.shopsTable',
                text: 'UberEats',
                link: '/client/settings/ubereats',
                permissions: 'clients-read'
            }],
        },
        {
            text: 'Product Management',
            key: 'menu.productManage',
            permissions: 'vats-read|product_groups-read|product_subgroups-read|products-read',
            items: [{
                    icon: 'mdi-cash',
                    key: 'menu.vatList',
                    text: 'Vat',
                    link: '/vats',
                    permissions: 'vats-read'
                },
                {
                    icon: 'mdi-code-greater-than',
                    key: 'menu.productGroupList',
                    text: 'Product Group',
                    link: '/product/group/list',
                    permissions: 'product_groups-read'
                },
                {
                    icon: 'mdi-code-greater-than-or-equal',
                    key: 'menu.productSubgroupList',
                    text: 'Product Subgroup',
                    link: '/product/subgroup/list',
                    permissions: 'product_subgroups-read'
                },
                {
                    icon: 'mdi-drawing-box',
                    key: 'menu.productList',
                    text: 'Product',
                    link: '/products',
                    permissions: 'products-read'
                },
            ]
        },
        {
            text: 'Optional Group Management',
            // key: 'menu.optionalGroupManage',
            permissions: 'optional_groups-read',
            items: [{
                icon: 'mdi-cash',
                // key: 'menu.optionalGroupList',
                text: 'Option Group List',
                link: '/optional-group/list',
                permissions: 'optional_groups-read'
            }, ]
        },
        {
            text: 'Menu Management',
            key: 'menu.menuManage',
            permissions: 'menu_categories-read|menus-read',
            items: [{
                    icon: 'mdi-cash',
                    key: 'menu.menuCategoryList',
                    text: 'Menu Category List',
                    link: '/menu/category/list',
                    permissions: 'menu_categories-read'
                },
                {
                    icon: 'mdi-cash',
                    // key: 'menu.menuList',
                    text: 'Menu List',
                    link: '/menu/list',
                    permissions: 'menus-read'
                },
            ]
        },
        /* {
             text: 'Addresses',
             key: 'menu.addresses',
             items: [
                 {
                     icon: 'mdi-map-marker-multiple-outline',
                     key: 'menu.addressesTable',
                     text: 'Table',
                     link: '/dashboard/addresses'
                 },
                 {
                     icon: 'mdi-map-marker-plus-outline',
                     key: 'menu.addressesCreate',
                     text: 'Create',
                     link: '/dashboard/addresses/create'
                 }
             ]
         },*/
        /*{
            text: 'Administration',
            key: 'menu.administration',
            noShopSelected: true,
            items: [{
                icon: 'mdi-source-fork',
                key: 'menu.orderSourcesTable',
                text: 'Order Sources',
                link: '/dashboard/administrations/order-sources',
            }, {
                icon: 'mdi-store-outline',
                key: 'menu.shopsTable',
                text: 'Shops',
                link: '/dashboard/administrations/shops',
            }]
        },*/
        {
            text: 'Operations [SuperAdmin]',
            key: 'menu.operations',
            permissions: 'order_source_types-read|payment_methods-read|order_statuses-read|order_types-read|courier_types-read|kitchens-read|tables-read|cancellation_reasons-read',
            items: [{
                    icon: 'mdi-file-outline',
                    key: 'menu.orderSourceTypesTable',
                    text: 'Order Types',
                    link: '/dashboard/operations/order-source-types',
                    permissions: 'order_source_types-read'
                },
                {
                    icon: 'mdi-credit-card',
                    key: 'menu.paymentMethod',
                    text: 'Payment Method',
                    link: '/dashboard/operations/payment-methods',
                    permissions: 'payment_methods-read'
                },
                {
                    icon: 'mdi-camera-timer',
                    key: 'menu.orderStatusesTable',
                    text: 'Order Statuses',
                    link: '/dashboard/operations/order-statuses',
                    permissions: 'order_statuses-read'
                },
                {
                    icon: 'mdi-nutrition',
                    key: 'menu.orderTypesTable',
                    text: 'Order Types',
                    link: '/dashboard/operations/order-types',
                    permissions: 'order_types-read'
                },
                {
                    icon: 'mdi-package-variant',
                    key: 'menu.courierType',
                    text: 'Courier Types',
                    link: '/dashboard/operations/courier-types',
                    permissions: 'courier_types-read'
                },
                // {
                //     icon: 'mdi-view-dashboard-outline',
                //     key: 'menu.dashboard',
                //     text: 'Dashboard',
                //     link: '/dashboard/analytics'
                // },
                // {icon: 'mdi-file-outline', key: 'menu.blank', text: 'Blank Page', link: '/blank'}

                {
                    icon: 'mdi-drawing-box',
                    key: 'menu.kitchenList',
                    text: 'Kitchen',
                    link: '/kitchen/list',
                    permissions: 'kitchens-read'
                },
                {
                    icon: 'mdi-drawing-box',
                    key: 'menu.tableList',
                    text: 'Table',
                    link: '/table/list',
                    permissions: 'tables-read'
                },
                {
                    icon: 'mdi-drawing-box',
                    key: 'menu.cancellationReasonList',
                    text: 'Cancellation Reason',
                    link: '/cancellation-reason/list',
                    permissions: 'cancellation_reasons-read'
                },
                {
                    icon: 'mdi-drawing-box',
                    key: 'menu.accountSettings',
                    text: 'Account Settings',
                    link: '/account/settings',
                    permissions: 'cancellation_reasons-read'
                }
            ]
        },
        {
            text: 'Customers',
            key: 'menu.customers',
            permissions: 'customers-read',
            items: [{
                icon: 'mdi-account',
                key: 'menu.customers',
                text: 'Customers',
                link: '/customers',
                permissions: 'customers-read'
            }]
        },
        {
            text: 'Management',
            key: 'menu.management',
            permissions: 'users-read|roles-read|permissions-read',
            items: [
                {
                    icon: 'mdi-account',
                    key: 'menu.users',
                    text: 'Users',
                    link: '/users',
                    permissions: 'users-read'
                }, {
                    icon: 'mdi-account-cowboy-hat',
                    key: 'menu.roles',
                    text: 'Roles',
                    link: '/roles',
                    permissions: 'roles-read'
                }, {
                    icon: 'mdi-account-key',
                    key: 'menu.permissions',
                    text: 'Permissions',
                    link: '/permissions',
                    permissions: 'permissions-read'
                }, {
                    icon: 'mdi-devices',
                    key: 'menu.devices',
                    text: 'Devices',
                    link: '/device',
                    permissions: 'device-read'
                }
            ]
        }
        // {
        //     text: 'Customers',
        //     key: 'menu.customers',
        //     items: [
        //         {icon: 'mdi-account-multiple', key: 'menu.customersTable', text: 'Table', link: '/dashboard/customers'},
        //     ]
        // },
        // {
        //     text: 'Pages',
        //     key: 'menu.pages',
        //     items: menuPages
        // }
    ],

    // footer links
    footer: [
        // {
        //     text: 'Docs',
        //     key: 'menu.docs',
        //     href: 'https://vuetifyjs.com',
        //     target: '_blank'
        // }
    ]
}
