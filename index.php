<?php

require 'vendor/autoload.php';

$app = new Slim\App();

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('./views/admin/');
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};


$app->get('/hello/{name}-{id}', function ($request, $response, $args) {
    return $this->view->render($response, 'dashboard.php', [
        'name' => $args['name'], 'id' => $args['id']
    ]);
})->setName('profile');

$app->run();