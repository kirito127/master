<?php
namespace App\Controllers;

class ApiController extends Controller{
    
    protected $container;
    protected $woocommerce;
    //protected $client;

    function __construct($container){
        $this->container = $container;
        //$this->client = $container->guzzlehttp;
        $this->woocommerce = new $this->container->woocommerce(
            'https://alla.ph',
            'ck_b7144d17091aa01a7a096154a445180c603dfc61',
            'cs_cdb7705d4ad5bf29aa2b6366c55ac98397e4ff07',
            [
                'wp_api'  => true,
                'version' => 'wc/v2',
            ]
        );
    }

    public function getProducts(){
        return json_encode($this->woocommerce->get('products/6730'));
    }

}