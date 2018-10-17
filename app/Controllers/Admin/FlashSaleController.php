<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\FlashSale;
use Slim\Views\Twig as View;

class FlashSaleController extends Controller{
    
    public function index($request, $response){
        return $this->view->render($response, 'admin/flashsale.twig');
    }

    // public function addToCart($req, $res, $arg){
    //     if(!isset($_SESSION['cart']) || $_SESSION['cart'] == null) $_SESSION['cart'] = array();

    //     $_SESSION['cart'][] = array(
    //         'id' => $arg['id'],'qty' => $arg['qty'],'name' => $arg['name']
    //     );
    //     $_SESSION['cart'] = $this->mergeDuplicateItems($_SESSION['cart']);

    //     return $this->loadCart();
    // }

    // protected function mergeDuplicateItems($arr){
    //     $sum = array_reduce( $arr, function ($a, $b) {
    //         isset($a[$b['id']]) ? $a[$b['id']]['qty'] += $b['qty'] : $a[$b['id']] = $b;  
    //         return $a;
    //     });
    //     return array_values($sum);
    // }

    // public function loadCart(){
    //     $template ='';
    //     if(count($_SESSION['cart'])){
    //         foreach($_SESSION['cart'] as $item){
    //             $template .="<ul class='list-group list-group-flush'>
    //                             <li class='list-group-item'>
    //                                 <span id='".$item['id']."' style='cursor:pointer' class='btn-trash float-right text-danger'><i class='fa fa-trash'></i></span>
    //                                 <small class='text-dark'>".$item['name']."</small><br>
    //                                 <small class='text-muted'>Qty: <span id='qtytext'>".$item['qty']."</span></small>
    //                             </li>
    //                         </ul>";
    //         }
    //         $template   .=  "<ul class='list-group list-group-flush cart-btn'>
    //                             <li class='list-group-item d-flex justify-content-end p-2'>
    //                                 <button id='cartclearbtn' class='btn btn-secondary btn-sm'>Clear</button>
    //                                 <button id='cartcheckoutbtn' class='btn btn-primary btn-sm ml-2'>Checkout</button>
    //                             </li>
    //                         </ul>";
    //     }else{
    //         $template = "<ul class='list-group list-group-flush'>
    //                         <li class='list-group-item'>
    //                             <span>Cart is empty.</span>
    //                         </li>
    //                     </ul>";
    //     }
    //     return json_encode(array('template' => $template, 'size' => array_sum(array_column($_SESSION['cart'], 'qty'))));
    // }

    // public function saveBasket(){
    //     $create = FlashSale::create([
    //         'Product_Id' => 1,
    //         'User_Id' => 1,
    //         'status' => 'pending',
    //         'start_date' => 'now()',
    //         'end_date' => 'now()',//date("Y-m-d H:i:s"),
    //         'sale_price' => 1000.00,
    //         'custom_sale_price' => 900.00
    //     ]);
    //     var_dump($create);
    // }

    public function addToBasket($req, $res, $arg){
        if(!isset($_SESSION['basket']) || $_SESSION['basket'] == null) $_SESSION['basket'] = array();

        if(!$this->checkExistence($arg['id'], $_SESSION['basket'], 'id'))
            $_SESSION['basket'][] = array(
                'id' => $arg['id'], 'name' => $arg['name']
            );
        return $this->loadBasket();
    }

    protected function checkExistence($in, $array, $key){
        $arr = array_column($array, $key);
        foreach($arr as $value){
            if($value == $in)
                return true;
        }
        return false;
    }

    public function loadBasket(){
        $arr = $_SESSION['basket']; $template = '';

        if(count($arr)){
            $template  .=  "<ul class='list-group list-group-flush basket-btn'>
                                <li class='list-group-item p-2 bg-secondary text-center'>
                                    <span><strong>". count($arr) ."</strong> product(s) selected</span>
                                </li>
                            </ul>";     

            foreach($arr as $item){
                $template .="<ul class='list-group list-group-flush'>
                                <li class='list-group-item'>
                                    <span id='".$item['id']."' style='cursor:pointer' class='btn-trash float-right text-danger'><i class='fas fa-times'></i></span>
                                    <small class='text-dark'>".$item['name']."</small>
                                </li>
                            </ul>";
            }

            $template  .=  "<ul class='list-group list-group-flush basket-btn'>
                                <li class='list-group-item p-2 d-flex justify-content-end'>
                                    <button id='basketclearbtn' class='btn btn-secondary btn-sm rounded'>Clear</button>
                                    <button id='basketsavebtn' class='btn btn-success btn-sm rounded ml-1'>Save</button>
                                </li>
                            </ul>";
        }else{
            $template = "<ul class='list-group list-group-flush'>
                            <li class='list-group-item'>
                                <span>Basket is empty.</span>
                            </li>
                        </ul>";
        }
        return json_encode(array('template' => $template, 'size' => count($arr)));
    }




    public function removeToBasket($req, $res, $arg){
        $id = $arg['id'];
        if($arg['id']){
            $_SESSION['basket'] = array_filter($_SESSION['basket'], function($x) use ($id) { return $x['id'] != $id; });
        }else{
            $_SESSION['basket'] = array();
        }
    }
}

?>