<?php

    // Creating routes

    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    // HOME ROUTE
    // 
    $app->get('/', function (Request $request, Response $response, $args)   {

        $vars = [
            'page' => [
            'title' => 'Dashboard',
            'description' => 'Dashboard Page'
            ],
        ];  


        return $this->view->render($response, 'admin/dashboard.twig', $vars);

    })->setName('dashboard');

    // ABOUT ROUTE
    // 
    $app->get('/product', function (Request $request, Response $response, $args)   {

        $vars = [
            'page' => [
            'title' => 'Product',
            'description' => 'Product Page'
            ],
        ];  

        return $this->view->render($response, 'admin/product.twig', $vars);

    })->setName('about');

    // ABOUT ROUTE
    // 
    $app->get('/contact', function (Request $request, Response $response, $args)   {

        $vars = [
            'page' => [
            'title' => 'About Us - Alpha Inc.',
            'description' => 'Drop us a line or get in touch for any enquires.'
            ],
        ];  

        return $this->view->render($response, 'contact.twig', $vars);

    })->setName('contact');