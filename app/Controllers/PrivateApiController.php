<?php
namespace App\Controllers;
use App\Models\User;
class PrivateApiController extends Controller{
    
    protected $container;
    protected $woocommerce;

    function __construct($container){
        $this->container = $container;
        $this->woocommerce = new $this->container->woocommerce(
            'https://alla.ph',
            'ck_b7144d17091aa01a7a096154a445180c603dfc61',
            'cs_cdb7705d4ad5bf29aa2b6366c55ac98397e4ff07',
            ['wp_api'  => true, 'version' => 'wc/v2']
        );
    }

    protected function template($id, $img, $name, $reg, $sale){
        return  "<div class='card product border-white text-white animated fadeIn rounded m-1'>
                    <input type='hidden' class='card-id' value='$id'>
                    <input type='hidden' class='card-name' value='$name'>
                    <img class='card-img' src='$img' alt='Card image'>
                    <div class='card-img-overlay product p-0 d-flex align-items-stretch'>
                        <div class='container p-2 m-0'>
                            <strong class='card-text'>$name</strong>
                            <p class='card-text'>
                                <small>Regular Price : ₱$reg</small><br>
                                <small>Sale Price : $sale</small>
                            </p>
                        </div>
                    </div>
                </div>";          
    }

    protected function assembleProduct($productArr){
        $template = '';
        if($productArr){
            foreach($productArr as $product){
                $name = $product->name;
                $id = $product->id;
                $reg = number_format((float)$product->regular_price, 2);
                $sale = $product->sale_price ? '₱'.  number_format((float)$product->sale_price, 2) : '-';
                $img = $product->images[0]->src;
                $template .= $this->template($id, $img, $name, $reg, $sale);
            }
        }else{
            $template = "<div class='alert alert-danger'>No product found.</div>";
        }
        return $template;
    }

    public function getProducts($req, $res, $arg){
        $filter =  !isset($arg['filter']) ?: $arg['filter'];
        $productArr = $this->woocommerce->get('products',array(
            'page'      => 1,
            'status'    => 'publish',
            'per_page'  => $arg['limit'],
            'search'    => $filter,
        ));

        $result = array(
            'template'  =>  $this->assembleProduct($productArr),
            'status'    =>  $productArr ? true : false,
        );

        return json_encode($result);
    }

    public function appendProducts($req, $res, $arg){
        $filter =  !isset($arg['filter']) ?: $arg['filter'];
        $productArr = $this->woocommerce->get('products',array(
            'page'      => $arg['page'],
            'status'    => 'publish',
            'per_page'  =>  $arg['limit'],
            'search'    => $filter,
        ));
        
        $result = array(
            'template'  =>  $this->assembleProduct($productArr),
            'status'    =>  $productArr ? true : false,
        );

        return json_encode($result);
    }

    public function extractCartSession(){
        $new = array(); $cart = $_SESSION['cart']; 
        foreach($cart as $item){
            $new[] = array(
                'product_id' => $item['id'],
                'quantity'  =>  $item['qty']
            );
        }
        return $new;
    } 

    protected function billingUserDetail(){
        $data = array(
            'payment_method' => 'dragonpay',
            'payment_method_title' => 'Dragonpay Online Payment',
            'set_paid' => true,
            'billing' => array(
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address_1' => '969 Market',
                'address_2' => '',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postcode' => '94103',
                'country' => 'PH',
                'email' => 'john.doe@example.com',
                'phone' => '(555) 555-5555'
            ),
            'shipping' => array(
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address_1' => '969 Market',
                'address_2' => '',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postcode' => '94103',
                'country' => 'US'
            ),
            'line_items' => $this->extractCartSession(),
        );
        return $data;
    }

    public function checkout(){
       $result = $this->woocommerce->post('orders', $this->billingUserDetail());
       $this->getProducts();
       var_dump($result);
    }

    protected function clearCart(){
        $_SESSION['cart'] = array();
    }
}