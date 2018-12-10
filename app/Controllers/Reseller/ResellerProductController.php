<?php
namespace App\Controllers\Reseller;

use App\Models\ResellerProducts;
use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;

class ResellerProductController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'reseller/product.twig');

    }

    protected function assembleProductRow($products){
        $temp = '';
        if($products){
            foreach($products as $product){
                $temp .= "<tr class='table-". ($product->status =='Pending' ? 'warning': '') ."'>
                            <td>". $product->code ."</td>
                            <td>". $product->name ."</td>
                            <td>". $product->status ."</td>
                            <td><i class='fas fa-ellipsis-h'></i></td>
                        </tr>";
            }
        }else{
            $temp = "<tr class='table-danger'><td colspan='5'>No product found</td></tr>";
        }
        return $temp;
    }

    public function loadProduct($req, $res, $args){
        $limit      = $args['limit'];
        $filter     = $args['filter'];
        try{
            
            $products = ResellerProducts::all()->take($limit);

            if($filter){
                //a$products = $products->where('code', 'LIKE', "%$filter%");
            }

            $result = array(
                'temp'  => $this->assembleProductRow($products),
                'status'=> ($products ? true : false),
            );

            return json_encode($result);

        }catch(\Exception $e){
            return json_encode($e);
        }
        
    }

    public function registerProduct($req, $res, $args){

        try{
            $result = array();
            $product = array(
                'Reseller_Id'   => $_SESSION['userid'],
                'code'          => $args['code'],
                'name'          => $args['name'],
                'status'        => 'Pending',
            );
            $create = ResellerProducts::insert($product);
            if($create){
                $result = array(
                    'status'    => true,
                    'temp'  => $this->Dagger->alertTemp('success', '<strong>Well done !</strong> Product is now on pending list and need to be confirm by the admin.')
                );
            }else{
                $result = array(
                    'status'    => false,
                    'temp'      => $this->Dagger->alertTemp('danger', '<strong>Opss !</strong> Some error occured, Please try again.')
                );
            }
            return json_encode($result);

        }catch(\Exception $e){
            return json_encode($e);
        }

    }
}

?>