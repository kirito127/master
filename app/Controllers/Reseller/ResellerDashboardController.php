<?php
namespace App\Controllers\Reseller;

use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;

class ResellerDashboardController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'reseller/dashboard.twig');

    }
}

?>