<?php

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'timeout' => env('RABBITMQ_TIMEOUT', 10.0),
    'exchanges' => [
        'commands' => [
            'name' => 'unilab.commands',
            'type' => 'topic',
            'durable' => true,
            'auto_delete' => false,
        ],
        'updates' => [
            'name' => 'unilab.updates',
            'type' => 'fanout',
            'durable' => true,
            'auto_delete' => false,
        ],
    ],
];
