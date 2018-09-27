<?php
use Psr\Container\ContainerInterface;

class ApiController{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function sample($request, $response, $args){
        return isset($args['name']) ? $args['name'] : 'empty';
    }
}


?>