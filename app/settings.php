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
                    'driver'    =>  'mysql',
                    'host'      =>  'localhost',
                    'database'  =>  'dealahos_alla',
                    'username'  =>  'root',
                    'password'  =>  '',
                    'charset'   =>  'utf8',
                    'collation' =>  'utf8_unicode_ci',
                    'prefix'    =>  'wpfw',
                ]

            ]
    ];