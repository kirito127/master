<?php
    // Creating routes
    // Psr-7 Request and Response interfaces
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;
    use App\Middleware\AuthMiddleWare;
    use App\Middleware\GuestMiddleWare;
    use App\Middleware\VendorMiddleWare;

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
        $this->get('/voucher','VoucherController:index')->setName('voucher');
    })->add(new AuthMiddleWare($container));

    $app->group('', function(){
        $this->get('/merchant','MerchantDashboardController:index')->setName('vendor.dashboard');
        $this->get('/merchant/voucher-search','VoucherSearchController:index')->setName('vendor.voucher-search');
        $this->get('/merchant/voucher-records','VoucherRecordsController:index')->setName('vendor.voucher-records');
    })->add(new VendorMiddleware($container));

    $app->group('', function(){
        $this->get('/auth/login','AuthController:getLogin')->setName('auth.login');
        $this->post('/auth/login','AuthController:postLogin')->setName('auth.login');
    })->add(new GuestMiddleWare($container));

    $app->get('/auth/logout','AuthController:getLogout')->setName('auth.logout');

    //  ******* API ROUTES  ******* //

    // $app->get('/api/product','ApiController:getProducts');

    //  ******* END API ROUTES  ******* //

    //  ******* PRIVATE API ROUTES  ******* //
    $app->get('/papi/product/append/{limit}/{page}[/{filter}]','FlashSaleController:appendProducts');
    $app->get('/papi/product/{limit}[/{filter}]','FlashSaleController:getProducts');
    $app->get('/papi/basket/remove/{id}','FlashSaleController:removeToBasket');
    $app->get('/papi/basket/save','FlashSaleController:saveBasket');
    $app->get('/papi/basket/add/id={id}/name={name}','FlashSaleController:addToBasket');
    $app->get('/papi/basket','FlashSaleController:loadBasket');
    $app->get('/papi/checkout','FlashSaleController:checkout');
    $app->get('/papi/schedule/{status}','FlashSaleController:loadSchedule');
    $app->get('/papi/schedule/delete/{id}','FlashSaleController:deleteSchedule');
    $app->get('/api/vendor','ProductsController:getVendor');
    $app->get('/admin/voucher/vendor','VoucherController:getVendor');
    $app->get('/admin/voucher/voucher/{id}/{limit}/{status}/{datefrom}/{dateto}','VoucherController:loadVoucher');
    $app->get('/admin/voucher/order/{ordernum}','VoucherController:getOrder');
    $app->get('/admin/voucher/email/{ordernum}/{vouchercode}/{voucherid}/{fname}/{lname}/{email}/{phone}/{note}','VoucherController:sendEmail');

    $app->get('/merchant/voucher-search/search/{code}','VoucherSearchController:searchVoucher');
    $app->get('/merchant/voucher-search/update/{id}','VoucherSearchController:updateVoucher');
    $app->get('/merchant/voucher-records/load/{limit}/{filter}/{datefrom}/{dateto}','VoucherRecordsController:loadVoucher');
    $app->get('/merchant/voucher-records/load-mobile/{limit}/{filter}/{datefrom}/{dateto}','VoucherRecordsController:loadVoucherMobile');
    $app->get('/merchant/voucher-records/view-mobile/{id}','VoucherRecordsController:viewVoucherMobile');
    $app->get('/merchant/voucher-view/{id}','VoucherViewController:index')->setName('vendor.voucher-view');
    $app->get('/merchant/dashboard/{type}/{datefrom}/{dateto}', 'MerchantDashboardController:loadGraph');



    //  ******* ENDPRIVATE API ROUTES  ******* //


