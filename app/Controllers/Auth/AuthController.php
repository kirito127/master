<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use Slim\Views\Twig as View;
use Respect\Validation\Validator as v;

class AuthController extends Controller{

    public function getLogin($request, $response){
        return $this->view->render($response, 'auth/login.twig');
    }

    public function postLogin($request, $response){
        $validation = $this->validator->validate($request, [
            'email'     => v::noWhitespace()->notEmpty()->email(),
            'password'  => v::notEmpty(),
        ]);
        
        if($validation->failed()){
           return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        //echo json_encode($request->getParam('email'));
        return $response->withRedirect($this->router->pathFor('dashboard'));
    }

}