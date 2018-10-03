<?php

// Get the container
$container = $app->getContainer();

//eloquent dependency
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal(); $capsule->bootEloquent();
$container['db'] = function($container) use ($capsule){
    return $capsule;
};

// Twig view dependency
$container['view'] = function ($c) {
    $cf = $c->get('settings')['view'];
    $view = new \Slim\Views\Twig($cf['path'], $cf['twig']);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));
    return $view;
};

// woo dependency
$container['Client'] = function($container){
    $woocommerce = new Automattic\WooCommerce\Client; 
    return $woocommerce;
};

//guzzle dependency
$container['HttpClient'] = function ($container) {
    $client = new \GuzzleHttp\Client();
    return $client;
};

$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};

$container['DashboardController'] = function($container){
    return new \App\Controllers\DashboardController($container->HttpClient);
};

$container['ApiController'] = function($container){
    return new \App\Controllers\ApiController($container);
};

$container['validator'] = function($container){
    return new \App\Validation\Validator;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldLoginInputMiddleware($container));


