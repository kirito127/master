<?php
namespace App\Controllers\Vendor;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class DashboardController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/dashboard.twig');

    }
}