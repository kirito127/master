<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\FlashSale;
use Slim\Views\Twig as View;

class FlashSaleController extends Controller{

    public function index($request, $response){
        return $this->view->render($response, 'admin/flashsale.twig');
    }

    public function deleteSchedule($req, $res, $arg){
        $result = FlashSale::where('id', $arg['id'])->delete();
        return json_encode($result);
    }

    protected function assembleSchedule($schedules){
        $temp = '';
        if(count($schedules)){
            foreach($schedules as $schedule){
                $badge ='';

                switch($schedule['status']){
                    case 'Pending': $badge='warning'; break;
                    case 'Success': $badge='success'; break;
                    case 'Expired': $badge='danger'; break;
                    default: break;}

                $temp .="<tr id='row". $schedule['id'] ."'>
                            <td>
                                <div class='form-check'>
                                    <label class='form-check-label'>
                                    <input type='checkbox' value='". $schedule['id'] ."' class='form-check-input'>
                                    </label>
                                </div>
                            </td>
                            <td>". $schedule['Product_Id'] ."</td><td>". $schedule['User_Id'] ."</td>
                            <td><small class='badge badge-pill badge-$badge'>". $schedule['status'] ."</small></td>
                            <td>". $schedule['sale_price'] ."</td><td>". $schedule['custom_sale_price'] ."</td>
                            <td>". $schedule['start_date'] .' - '. $schedule['end_date'] ."</td>
                            <td>". $schedule['set_date'] ."</td>
                            <td>
                                <button id='". $schedule['id'] ."' class='btn btn-danger btn-sm btn-deleteschedule'><i class='far fa-trash-alt'></i></button>
                                <button id='". $schedule['id'] ."' class='btn btn-warning btn-sm btn-editschedule'><i class='far fa-edit'></i></button>
                            </td>
                        </tr>";
            }
        }else{
            $temp = "<tr class='table-danger text-center'><td colspan='9'>No record found.</td></tr>";
        }

        return $temp;
    }
    public function loadSchedule($req, $res, $arg){
        $type = $arg['status'];
        $scheduleArr;
        switch($type){
            case 'Success':
                $scheduleArr = FlashSale::where('status', 'Success')->get();
            break;
            case 'Pending':
                $scheduleArr = FlashSale::where('status', 'Pending')->get();
            break;
            case 'Duplicate':
                //$scheduleArr = FlashSale::where('end_date', '<', $this->container->Dagger->datenow())->get();
            break;            
            case 'Expired':
                $scheduleArr = FlashSale::whereRaw('YEAR(`end_date`) != 0')
                                        ->where('end_date', '<', $this->container->Dagger->datenow())
                                        ->where('status', 'Success')->get();                
            break;
            default: 
                $scheduleArr = FlashSale::all();
            break;
        }

        return $this->assembleSchedule($scheduleArr);
    }

    public function appendProducts($req, $res, $arg){
        $filter =  !isset($arg['filter']) ?: $arg['filter'];
        $productArr = $this->container->Api->getProducts(array(
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

    public function saveBasket($request, $response){
        $err = $this->container->Dagger->alertTemp('danger', 
        '<strong>Oops!</strong> Something unexpected happened, Please try again.');
        $success = $this->container->Dagger->alertTemp('success', 
        "<strong>Perfect!</strong> You've successfully saved the products");

        try {  
            $basketarr = $this->basketToArray();
            $create = FlashSale::insert($basketarr);
        } catch (\Illuminate\Database\QueryException $e) {
            return  $err;
        } catch (\Exception $e) {
            return  $err;
        }
        clearCart();
        return $create ? $success : $err;
    }

    protected function clearCart(){
        $_SESSION['cart'] = array();
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

    protected function basketToArray(){
        $ids = array_column($_SESSION['basket'], 'id');
        $products = $this->container->Api->getProductsByIds(implode(',', $ids));
        $itemarr =array();
        foreach($products as $product){
            $itemarr[] = array(
                'Product_Id'        => $product->id,
                'user_Id'           => $_SESSION['userid'],     
                'status'            => 'Pending',
                'sale_price'        => $product->sale_price,
                'custom_sale_price' => 0,
            );
        }   
        return $itemarr;
    }

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
    
    protected function productCardTemp($id, $img, $name, $reg, $sale){
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
                $template .= $this->productCardTemp($id, $img, $name, $reg, $sale);
            }
        }else{
            $template = "<div class='alert alert-danger'>No product found.</div>";
        }
        return $template;
    }

    public function getProducts($req, $res, $arg){
        try{
            $filter =  !isset($arg['filter']) ?: $arg['filter'];
            $productArr = $this->container->Api->getProducts(array(
                'page'      => 1,
                'status'    => 'publish',
                'per_page'  => $arg['limit'],
                'search'    => $filter,
            ));

            $result = array(
                'template'  =>  $this->assembleProduct($productArr),
                'status'    =>  $productArr ? true : false,
            );
        } catch (\Illuminate\Database\QueryException $e) {
            return  $e;
        }catch(\Exception $e){
            return $e;
        }
        return json_encode($result);
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
       $result = $this->Api->post('orders', $this->billingUserDetail());
       $this->getProducts();
       var_dump($result);
    }

}