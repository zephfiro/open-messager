<?php

return [
    'producer' => [
        'brokers'            => env('KAFKA_BROKERS', 'localhost:9092'),
        'acks'               => env('KAFKA_ACKS', -1),
        'enable_idempotence' => env('KAFKA_ENABLE_IDEMPOTENCE', true),
        'message_timeout_ms' => env('KAFKA_MESSAGE_TIMEOUT_MS', 10000),
    ],
];
