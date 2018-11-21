<?php
namespace App\Controllers\Vendor;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\User;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class MerchantProductsController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/products.twig');
    }

    public function getVendor(){
        $result = '';
        try{
            $result = $this->container->Api->getVendors();
        }catch(\Exception $e){
            echo $e;
        }
        return $result;
    }

    public function loadProducts($req, $res, $args){
        $products = $this->Api->getVendorProducts($_SESSION['userid']);
        return $this->TemplateProducts($products);
    }

    public function TemplateProducts($products){
        $template ='';
        
        foreach($products as $products){
            $price = '';
            if($products->sale_price != 0){
                $price = 'P'. number_format($products->sale_price,2);
            }else{
               $price = $products->regular_price ? 'P'. number_format($products->regular_price,2): 'Variation Product';
            }

            $template .="<tr>
                            <td>". $products->id ."</td>
                            <td class='p-1'><img style='max-height:40px;' class='img-avatar m-0' src='". $products->images[0]->src ."'></td>
                            <td>". $products->name ."</td>
                            <td>". ( $products->manage_stock ? $products->stock_quantity : 'Available') ."</td>
                            <td> ". $price." </td>
                        </tr>";
        }
        return $template;
    }

    
}