<?php
namespace App\Controllers\Admin;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class DashboardController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'admin/dashboard.twig');

        // $user = $this->db->table('users')->find(1);
        // echo json_encode($user->display_name);

    //     $user = User::where('id', '2');
    //     var_dump($user);
    //     die();
    }
}

?>