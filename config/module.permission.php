<?php

return [
    'api'   => [
        [
            'module'      => 'content',
            'section'     => 'api',
            'package'     => 'item',
            'handler'     => 'item',
            'permissions' => 'dashboard',
            'role'        => [
                'member',
                'admin',
            ],
        ],
    ],
];