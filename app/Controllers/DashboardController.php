<?php
namespace App\Controllers;

//require 'Controller.php';
use App\Controllers\DashboardController;
use App\Models\User;
use Slim\Views\Twig as View;
use GuzzleHttp\Client;

class DashboardController extends Controller{
    
    public function index($request, $response){
        $this->flash->addMessage('info', '');
        return $this->view->render($response, 'admin/dashboard.twig');

        // $user = $this->db->table('users')->find(1);
        // echo json_encode($user->display_name);

    //     $user = User::where('id', '2');
    //     var_dump($user);
    //     die();
    }
}

?>