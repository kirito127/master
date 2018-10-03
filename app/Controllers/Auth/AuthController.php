<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use Slim\Views\Twig as View;
use Respect\Validation\Validator as v;
class AuthController extends Controller{

    public function getLogout($request, $response){
        $this->auth->logout();
        return $response->withRedirect($this->router->pathFor('auth.login'));

    }

    public function getLogin($request, $response){
        return $this->view->render($response, 'auth/login.twig');
    }

    public function postLogin($request, $response){
        // $validation = $this->validator->validate($request, [
        //     'email'     => v::noWhitespace()->notEmpty()->email(),
        //     'password'  => v::notEmpty(),
        // ]);
        
        // if($validation->failed()){
        //    return $response->withRedirect($this->router->pathFor('auth.login'));
        // }

        //echo json_encode($request->getParam('email'));
        //return $response->withRedirect($this->router->pathFor('dashboard'));

        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(!$auth){
            $this->flash->addMessage('login-error', 'The username/email or password youve enter is incorrect, Please try again.');
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        return $response->withRedirect($this->router->pathFor('dashboard'));
    }

}