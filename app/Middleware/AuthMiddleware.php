<?php
namespace App\Middleware;

class AuthMiddleware extends Middleware{

    public function __invoke($request, $response, $next){

        if(!$this->container->auth->check()){
            $this->container->flash->addMessage('login-error', 'Please sign in.');
           return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }elseif($this->container->auth->role() !== 'administrator'){
            return $response->withStatus(403);
        }

        $response = $next($request, $response);
        return $response;
    }

}