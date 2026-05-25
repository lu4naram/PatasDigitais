<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Cloudflare R2 - CONFIGURAÇÃO CORRETA
        'r2' => [
            'driver' => 's3',
            'key' => env('R2_KEY'),
            'secret' => env('R2_SECRET'),
            'region' => env('R2_REGION', 'auto'),
            'bucket' => env('R2_BUCKET'),
            'endpoint' => env('R2_ENDPOINT'),
            'url' => env('R2_URL'),  // ADICIONE ESTA LINHA
            'visibility' => 'public',
        ],
    ],

    'cloud' => env('FILESYSTEM_CLOUD', 'r2'),
];