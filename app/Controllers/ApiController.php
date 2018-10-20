<?php
namespace App\Controllers;
use  \Automattic\WooCommerce\Client as Woo;
class ApiController extends Controller{

    public $wwoocommerceo;

    function __construct(){
        $this->woocommerce = new Woo(
            'https://alla.ph',
            'ck_b7144d17091aa01a7a096154a445180c603dfc61',
            'cs_cdb7705d4ad5bf29aa2b6366c55ac98397e4ff07',
            [
                'wp_api'  => true,
                'version' => 'wc/v2',
            ]
        );
    }

    public function getProducts($arg = array()){
        $result = $this->woocommerce->get('products', $arg);
        return  $result ?  $result : false;
    }

    public function getProductsByIds($id){
        $str = 'products/?include=';
        if(is_array($id)){
            $str .= explode(',', $id);
        }elseif(is_int($id) || is_string($id)){
            $str .= $id;
        }
        $result = $this->woocommerce->get($str);
        return  $result ?  $result : false;
    }

    public function placeOrder($billing){
        if($billing){
            $result = $this->woocommerce->get('orders', $billing);
            return $result ? $result : false;
        }
        return false;
    }

}