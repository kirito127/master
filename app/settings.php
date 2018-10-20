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
                    'host'      =>  'alla.ph',
                    'database'  =>  'dealahos_alla',
                    'username'  =>  'dealahos_user',
                    'password'  =>  'kirito127',
                    'charset'   =>  'utf8',
                    'collation' =>  'utf8_unicode_ci',
                    'prefix'    =>  'wpfw_',
                ]

            ]
    ];