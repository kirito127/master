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

$container['DashboardController'] = function($container){
    return new \App\Controllers\DashboardController($container);
};

// $woocommerce = require __DIR__ . '/../app/Wooapi/woosettings.php';

$container['ApiController'] = function($container){
return new \App\Controllers\ApiController($container);
};