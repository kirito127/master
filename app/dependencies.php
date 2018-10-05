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


// auth dependency
$container['auth'] = function($container){
    return new \App\Auth\Auth($container);
};

//flash dependency
$container['flash'] = function($container){
    return new \Slim\Flash\Messages;
};

// Twig view dependency
$container['view'] = function ($container) {
    $cf = $container->get('settings')['view'];
    $view = new \Slim\Views\Twig($cf['path'], $cf['twig']);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user'  => $container->auth->user(),
        // 'role'  => $container->auth->role(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};

// woo dependency
$container['Client'] = function($container){
    $woocommerce = new Automattic\WooCommerce\Client; 
    return $woocommerce;
};

//guzzle dependency
// $container['guzzlehttp'] = function ($container) {
//     $client = new \GuzzleHttp\Client();
//     return $client;
// };

$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};

$container['DashboardController'] = function($container){
    return new \App\Controllers\Admin\DashboardController($container);
};

$container['ProductController'] = function($container){
    return new \App\Controllers\Admin\ProductController($container);
};

$container['OrderController'] = function($container){
    return new \App\Controllers\Admin\OrderController($container);
};

$container['ApiController'] = function($container){
    return new \App\Controllers\ApiController($container);
};

$container['PrivateApiController'] = function($container){
    return new \App\Controllers\PrivateApiController($container);
};

$container['validator'] = function($container){
    return new \App\Validation\Validator;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldLoginInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$container['csrf'] = function($container){
    return new \Slim\Csrf\Guard;
};
$app->add($container->csrf);


$container['passwordhasher'] = function($container){
    return new \App\Auth\PasswordHash(8, TRUE);
};

$container['woocommerce'] = function($container){
    return new \Automattic\WooCommerce\Client(null, null, null, null);
};

