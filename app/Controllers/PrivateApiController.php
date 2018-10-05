<?php
namespace App\Controllers;

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

    public function getProducts(){
        // return json_encode($this->woocommerce->get('products?category=1446')); filter by category id is working
        $productArr = $this->woocommerce->get('products?status=publish&per_page=5');
        $template = '';
        foreach($productArr as $product){
            $name = $product->name;
            $id = $product->id;
            $reg = number_format((float)$product->regular_price, 2);
            $sale = $product->sale_price ? '₱'.  number_format((float)$product->sale_price, 2) : '-';
            $img = $product->images[0]->src;
            $template .= "<div class='card mx-1' style='max-width:11.5rem;' data-toggle='modal'>
                        <input type='hidden' class='card-id' value='$id'>
                        <img class='card-img-top img-rounded' style='max-height:200px' src='$img' alt='x'>
                        <div class='card-body p-1'>
                            <a href='#' class='card-text text-primary my-0'>$name</a><br>
                            <small class='card-text m-0 text-muted'>ID : $id</small><br>
                            <small class='card-text m-0'>Regular Price : ₱$reg</small><br>
                            <small class='card-text m-0'>Sale Price : $sale</small>
                        </div>
                        </div>";
        }
        return $template;
    }

    public function addToCart($req, $res, $arg)
    {
        $template ='';
        if(!isset($_SESSION['cart'])) $_SESSION['cart'] = array();

        if(empty($arg)){
            $_SESSION['cart'][] = array(
                'id' => $arg['id'],'qty' => $arg['qty'],'name' => $arg['name']
            );
        }

        $sum = array_reduce( $_SESSION['cart'], function ($a, $b) {
            isset($a[$b['id']]) ? $a[$b['id']]['qty'] += $b['qty'] : $a[$b['id']] = $b;  
            return $a;
        });
        $_SESSION['cart'] = array_values($sum);

        foreach($_SESSION['cart'] as $item){
            $template .="<ul class='list-group list-group-flush'>
                            <input type='hidden' value='".$item['id']."'>
                            <li class='list-group-item'>
                                <span style='cursor:pointer' class='float-right text-danger'><i class='fa fa-trash'></i></span>
                                <small class='text-dark'>".$item['name']."</small><br>
                                <small class='text-muted'>Qty: <span id='qtytext'>".$item['qty']."</span></small>
                            </li>
                        </ul>";
        }
        return json_encode(array('template' => $template, 'size' => array_sum(array_column($_SESSION['cart'], 'qty'))));
    }

    public function removeToCart($req, $res, $arg){
        if(empty($arg)){
            $_SESSION['cart'] = array();
            return 'all';

        }else{
            foreach($_SESSION['cart'] as $key => $value){
                if($key['id'] == $arg['id']){
                    unset($_SESSION['cart'][$key]);
                }
            }
            return 'all '. $arg['id'];
        }
    }
}