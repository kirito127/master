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
        $ids    = json_decode($arg['id'], true);
        $ids    = explode(',', $ids);
        try{
            // $org->products()->find($ids)->each(function ($product, $key) {
            //     $product->delete();
            // });


            //$result = FlashSale::whereIn('id',$ids)->delete();

            $result = FlashSale::find($ids)->each(function($item, $key){
                $item->delete();
            });

        return json_encode($result);
        }catch(\Exception $e){
            return $e->getMessage();
        }

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

                $temp .="<tr id='". $schedule['id'] ."'>
                            <td>
                                <div class='form-check'>
                                    <label class='form-check-label'>
                                    <input type='checkbox' value='". $schedule['id'] ."' class='form-check-input'>
                                    </label>
                                </div>
                            </td>
                            <td>". $schedule['Product_Id'] ."</td>
                            <td><small class='badge badge-pill badge-$badge'>". $schedule['status'] ."</small></td>
                            <td>P". number_format($schedule['sale_price'], 2) ."</td><td>P". number_format($schedule['custom_sale_price'], 2) ."</td>
                            <td>". ($schedule['start_date'] != 0 ? date('M j, Y', strtotime($schedule['start_date'])) : 'N/A') .' - '. ($schedule['end_date'] != 0 ? date('M j, Y', strtotime($schedule['end_date'])) : 'N/A') ."</td>
                            <td>". ($schedule['start_date'] != 0 ? date('M j, Y', strtotime($schedule['set_date'])) : 'N/A') ."</td>
                            <td>
                                <div class='dropdown'>
                                    <i class='fas fa-ellipsis-h text-dark' data-toggle='dropdown'></i>
                                    <div class='dropdown-menu'>
                                        <a id='". $schedule['id'] ."' class='btn-edit dropdown-item' href='javascript:void(0)'><i class='far fa-edit'></i> Edit</a>
                                        <a id='". $schedule['id'] ."' class='btn-delete dropdown-item' href='javascript:void(0)'><i class='far fa-trash-alt'></i> Delete</a>
                                    </div>
                                </div>
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
        // $err = $this->container->Dagger->alertTemp('danger', 
        // '<strong>Oops!</strong> Something unexpected happened, Please try again.');
        // $success = $this->container->Dagger->alertTemp('success', 
        // "<strong>Perfect!</strong> You've successfully saved the products");
        // $create = null;
        // try {  
        //     $basketarr = $this->basketToArray();
        //     $create = FlashSale::insert($basketarr);
        // } catch (\Illuminate\Database\QueryException $e) {
        //     $err = $e;
        // } catch (\Exception $e) {
        //     $err = $e->getMessage();
        // }
        // $this->clearCart();
        // return $create ? $success : $err;
        return json_encode($this->basketToArray());
    }
    
    protected function basketToArray(){

            $ids = array_column($_SESSION['basket'], 'id');
            $products = $this->Api->getProductsByIds(implode(',', $ids));
            $itemarr = array();
            // foreach($products as $product){
            //     $itemarr[] = array(
            //         'Product_Id'        => $product->id,
            //         'user_Id'           => $_SESSION['userid'],     
            //         'status'            => 'Pending',
            //         'sale_price'        => $product->sale_price,
            //         'custom_sale_price' => 0,
            //     );
            // }   
            return $products;

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

        try{
            $arr = isset($_SESSION['basket']) ? $_SESSION['basket'] : $_SESSION['basket']; 
            $size = ($arr ? count($arr) : 0);
            $template = '';
    
            if($size){
                $template  .=  "<ul class='list-group list-group-flush basket-btn'>
                                    <li class='list-group-item p-2 bg-secondary text-center'>
                                        <span><strong>". $size  ."</strong> product(s) selected</span>
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
           return json_encode(array('template' => $template, 'size' => $size ));
        
        }catch(\Exception $e){
            return json_encode($e->getMessage());
        }

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
            $filter =  (!isset($arg['filter']) ?: $arg['filter']);
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
        }catch(\Exception $e){
            return json_encode($e->getMessage());
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