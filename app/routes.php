<?php
    // Creating routes
    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    $app->get('/', function (Request $request, Response $response, $args)   {
        $vars = [
            'page' => [ 'title' => 'Dashboard', 'description' => 'Dashboard Page' ],
        ];  
        return $this->view->render($response, 'admin/dashboard.twig', $vars);
    })->setName('dashboard');

    $app->get('/product', function (Request $request, Response $response, $args)   {
        $vars = [
            'page' => [ 'title' => 'Product', 'description' => 'Product Page'],
        ];  
        return $this->view->render($response, 'admin/product.twig', $vars);
    })->setName('product');

    $app->get('/api/product/[{name}]',ApiController::class.':sample', function(Request $request, Response $response, $args){
    });
