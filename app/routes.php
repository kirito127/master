<?php
    // Creating routes
    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    $app->get('/','DashboardController:index');

    $app->get('/product', function (Request $request, Response $response, $args)   {
        $vars = [
            'page' => [ 'title' => 'Product', 'description' => 'Product Page'],
        ];  
        return $this->view->render($response, 'admin/product.twig', $vars);
    })->setName('product');

    $app->get('/api/product','ApiController:index');

