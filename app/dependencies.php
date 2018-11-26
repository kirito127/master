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

//override 404 page
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response->withStatus(404), '404.twig', [
            "myMagic" => "Let's roll"
        ]);
    };
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
        'role'  => $container->auth->role(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};

$container['Dagger'] = function($container){
    return new \App\Controllers\Dagger($container);
};

$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};

$container['DashboardController'] = function($container){
    return new \App\Controllers\Admin\DashboardController($container);
};

$container['ProductController'] = function($container){
    return new \App\Controllers\Admin\ProductController($container);
};

$container['FlashSaleController'] = function($container){
    return new \App\Controllers\Admin\FlashSaleController($container);
};

$container['VoucherController'] = function($container){
    return new \App\Controllers\Admin\VoucherController($container);
};

$container['Api'] = function($container){
    return new \App\Controllers\ApiController($container);
};

$container['Email'] = function($container){
    return new \App\Controllers\EmailController($container);
};

//vendor dep
$container['DashboardController'] = function($container){
    return new \App\Controllers\Vendor\DashboardController($container);
};

$container['MerchantProductsController'] = function($container){
    return new \App\Controllers\Vendor\MerchantProductsController($container);
};

$container['VoucherSearchController'] = function($container){
    return new \App\Controllers\Vendor\VoucherSearchController($container);
};

$container['VoucherRecordsController'] = function($container){
    return new \App\Controllers\Vendor\VoucherRecordsController($container);
};

$container['VoucherViewController'] = function($container){
    return new \App\Controllers\Vendor\VoucherViewController($container);
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


