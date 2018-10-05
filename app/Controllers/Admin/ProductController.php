<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class ProductController extends Controller{
    
    public function index($request, $response){
        return $this->view->render($response, 'admin/product.twig');
    }
}

?>