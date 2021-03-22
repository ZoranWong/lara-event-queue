<?php

return [
    'listeners' => [

    ],
    'queue' => env('LARA_EVENT_QUEUE', 'lara-event-queue'),
    'connection' => env('LARA_EVENT_QUEUE_CONNECTION', 'lara-event-queue-connection'),
    'redis' => env('LARA_EVENT_QUEUE_REDIS_CONNECTION', 'default')
];
