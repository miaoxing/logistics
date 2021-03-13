<?php

return [
    '/admin' => [
        '/shipping-tpls' => [
            'name' => '运费模板管理',
            '/new' => [
                'name' => '添加',
            ],
            '/[id]' => [
                '/edit' => [
                    'name' => '编辑',
                ],
            ],
        ],
        '/logistics-addresses' => [
            'name' => '地址管理',
            '/new' => [
                'name' => '添加',
            ],
            '/[id]' => [
                '/edit' => [
                    'name' => '编辑',
                ],
            ],
        ],
    ],
];
