<?php
namespace App\Controllers\Admin;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class OrderController extends Controller{
    
    public function index($request, $response){
        return $this->view->render($response, 'admin/order.twig');

    }
}

?>