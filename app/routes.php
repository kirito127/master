<?php
    // Creating routes
    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;
    use App\Middleware\AuthMiddleWare;

    $app->add(function (Request $request, Response $response, callable $next) {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));    
            if($request->getMethod() == 'GET') {
                return $response->withRedirect((string)$uri, 301);
            }
            else {
                return $next($request->withUri($uri), $response);
            }
        }
        return $next($request, $response);
    });
   


    $app->group('', function(){
        $this->get('/','DashboardController:index')->setName('dashboard');
    })->add(new AuthMiddleWare($container));


    $app->get('/auth/logout','AuthController:getLogout')->setName('auth.logout');



    
    $app->get('/auth/login','AuthController:getLogin')->setName('auth.login');
    $app->post('/auth/login','AuthController:postLogin')->setName('auth.login');

    $app->get('/product', function (Request $request, Response $response, $args)   {
        $vars = [
            'page' => [ 'title' => 'Product', 'description' => 'Product Page'],
        ];  
        return $this->view->render($response, 'admin/product.twig', $vars);
    })->setName('product');


    $app->get('/api/product','ApiController:getAllProducts');

