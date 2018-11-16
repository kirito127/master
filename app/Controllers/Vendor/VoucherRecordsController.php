<?php
namespace App\Controllers\Vendor;

use App\Models\Voucher;
use App\Controllers\Controller;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class VoucherRecordsController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/voucher-records.twig');
    }
    
}