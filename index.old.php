<?php
require_once './vendor/autoload.php';

$klein = new Klein\Klein;

$klein->respond(function ($request, $response, $service) {
    $service->slug = $request->pathname();
    $service->layout('layouts/default.php');
});

$klein->respond('GET', '/api/product', function ($request, $response) {
        return 'all products';
});

$klein->respond('/', function ($request, $response, $service) {
    $service->pageTitle = 'Dashboard Page';
    $service->render('views/admin/dashboard.php');
});

// Dashboard page view
$klein->respond('/dashboard', function ($request, $response, $service) {
    $service->pageTitle = 'Dashboard Page';
    $service->render('views/admin/dashboard.php');
});

// Product Page view
$klein->respond('/product', function ($request, $response, $service) {
    $service->pageTitle = 'Product Page';
    $service->render('views/admin/product.php');
});

// Login page view
$klein->respond('/login', function ($request, $response, $service) {
    $service->pageTitle = 'Login page';
    $service->render('views/admin/login.php');
});

// 404 page view
$klein->respond('/404', function ($request, $response, $service) {
    $service->pageTitle = 'Page not found';
    $service->render('views/404.php');
});

$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            header('Location:/404');
            exit();break;
        case 405:
            $router->response()->body(
                'You can\'t do that!'
            );
            break;
        default:
            $router->response()->body(
                'Oh no, a bad error happened that caused a '. $code
            );
    }
});

$klein->onHttpError(function ($code, $router) {
    if ($code >= 400 && $code < 500) {
        $router->response()->body(
            'Oh no, a bad error happened that caused a '. $code
        );
    } elseif ($code >= 500 && $code <= 599) {
        error_log('uhhh, something bad happened');
    }
});



$klein->dispatch();
?>