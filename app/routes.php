<?php
    // Creating routes
    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;
    use App\Middleware\AuthMiddleWare;
    use App\Middleware\GuestMiddleWare;

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
        $this->get('/product','ProductController:index')->setName('product');
        $this->get('/flashsale','FlashSaleController:index')->setName('flashsale');
    })->add(new AuthMiddleWare($container));


    $app->group('', function(){
        $this->get('/auth/login','AuthController:getLogin')->setName('auth.login');
        $this->post('/auth/login','AuthController:postLogin')->setName('auth.login');
    })->add(new GuestMiddleWare($container));

    $app->get('/auth/logout','AuthController:getLogout')->setName('auth.logout');

    //  ******* API ROUTES  ******* //

    // $app->get('/api/product','ApiController:getProducts');

    //  ******* END API ROUTES  ******* //

    //  ******* PRIVATE API ROUTES  ******* //
    $app->get('/papi/product/append/{limit}/{page}[/{filter}]','FlashSaleController:getProducts');
    $app->get('/papi/product/{limit}[/{filter}]','FlashSaleController:getProducts');
    $app->get('/papi/basket/remove/{id}','FlashSaleController:removeToBasket');
    $app->get('/papi/basket/save','FlashSaleController:saveBasket');
    $app->get('/papi/basket/add/id={id}/name={name}','FlashSaleController:addToBasket');
    $app->get('/papi/basket','FlashSaleController:loadBasket');
    $app->get('/papi/checkout','FlashSaleController:checkout');

    //  ******* ENDPRIVATE API ROUTES  ******* //


