<?php
namespace App\Middleware;

class AuthMiddleware extends Middleware{

    public function __invoke($request, $response, $next){

        if(!$this->container->auth->check()){
            $this->container->flash->addMessage('login-error', 'Please sign in.');
           return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }elseif($this->container->auth->role() == 'dc_vendor'){
           // return $response->withStatus(403);
           return $response->withRedirect($this->container->router->pathFor('vendor.dashboard'));
        }

        $response = $next($request, $response);
        return $response;
    }

}