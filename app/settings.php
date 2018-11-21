<?php

    return [
            'settings' => [
                'displayErrorDetails' => true,
                'view' => [
                    'path' => __DIR__ . '/Templates/views/',
                    'twig' => [
                    'cache' => false
                    ]
                ],
                'db' => [
                    'driver'    =>  env('DB_DRIVER'),
                    'host'      =>  env('DB_HOST'),
                    'database'  =>  env('DB_DATABASE'),
                    'username'  =>  env('DB_USERNAME'),
                    'password'  =>  env('DB_PASSWORD'),
                    'charset'   =>  'utf8',
                    'collation' =>  'utf8_unicode_ci',
                    'prefix'    =>  env('DB_PREFIX'),
                ]

            ]
    ];