<?php
namespace App\Controllers;
use  \Automattic\WooCommerce\Client as Woo;
use GuzzleHttp\Client as Guzz; 

class ApiController extends Controller{

    public $woocommerce; 
    public $guzzle;
    protected $url = 'https://alla.ph'; //woocommerce api
    protected $url2 = 'https://alla.ph/wp-json/wcmp/v1/'; //wcmp market api
    // protected $url3 = 'https://alla.ph/wp-json/wc/v2/'; //wcmp market wc api
    protected const CONSUMER_KEY = 'ck_b7144d17091aa01a7a096154a445180c603dfc61';
    protected const CONSUMER_SECRET = 'cs_cdb7705d4ad5bf29aa2b6366c55ac98397e4ff07';

    function __construct(){
        $this->woocommerce = new Woo(
            $this->url,
            self::CONSUMER_KEY,
            self::CONSUMER_SECRET,
            [
                'wp_api'  => true,
                'version' => 'wc/v2',
            ]
        );
        $this->guzzle = new Guzz(['base_uri' => $this->url2]);
    }

    protected function getAuth(){
        $auth = [ 'auth' => [self::CONSUMER_KEY, self::CONSUMER_SECRET] ];
        return $auth;
    }

    protected function switchUrl($version){
        $url3 = 'https://alla.ph/wp-json/wc/v'+ $version + '/';
        $this->guzzle = new Guzz(['base_uri' => $url3]);
    }

    public function getProducts($arg = array()){
        $result = $this->woocommerce->get('products', $arg);
        return  $result ?  $result : false;
    }

    public function getProductById($id){
        try{
            $result = $this->woocommerce->get("products/$id");
            return  $result ?  $result : false;
        }catch(\Exception $e){
            return $e->getMessage();
        }

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
    
    public function getVendors($id=0){
        $str = 'vendors/' . ($id ? $id : '');
        $result = $this->guzzle->request('GET', $str, $this->getAuth());
        echo $result->getBody();
    }

    public function getVendorProducts($id=0){
        if(!$id) return false;
        $this->switchUrl(2);
        $str = 'products/?vendor='. $id;
        $result = $this->guzzle->request('GET', $str, $this->getAuth());
        echo $result->getBody();
    }

    public function getOrders($ordernum = 0){
        $result = $this->woocommerce->get('orders/'. $ordernum);
        return $result ? $result : false;
    }

}