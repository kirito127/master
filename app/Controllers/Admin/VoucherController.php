<?php
namespace App\Controllers\Admin;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\Voucher;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class VoucherController extends Controller{
    
    public function index($res, $req){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($req, 'admin/voucher.twig',[]);
    }

    public function getOrder($res, $req, $args){
        try{
            $result = $this->container->Api->getOrders($args['ordernum']);
            $optiontemp ='<option> -- Select Product -- </option>';
            foreach($result->line_items as $items){
                $optiontemp .="<option value='". $items->product_id ."'>". $items->name ."</option>"; 
            }
    
            return json_encode(array(
                'optiontemp'    => $optiontemp, 
                'paymentmethod' => $result->payment_method
            ));
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function sendEmail($res, $req, $args){
        try{
            $voucherInfo = array(
                'id'            => $args['voucherid'],
                'vouchercode'   => $args['vouchercode'],
                'note'          => isset($args['note']) ? $args['note'] : '',
                'email'         => $args['email'],
                'ordernum'      => $args['ordernum'],
                'phone'         => isset($args['phone']) ? $args['phone'] : '',
            );
            $create = Voucher::insert($this->voucherData($voucherInfo));
            //return $this->assembleEmail($voucherInfo);
            return $create ? $create : 'error';
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    protected function voucherData($voucherInfo){ 
        $p = $this->Api->getProductById($voucherInfo['id']);
        $order = $this->container->Api->getOrders($voucherInfo['ordernum']);

        return array(
            'Product_Id'        => $voucherInfo['id'],
            'User_Id'           => $order->customer_id,
            'user_name'         => $order->billing->first_name ? $order->billing->first_name : $order->shipping->first_name,
            'Order_Id'          => $voucherInfo['ordernum'],
            'Vendor_Id'        => $p->vendor,
            'code'              => $voucherInfo['vouchercode'],
            'note'              => $voucherInfo['note'],
            'email'             => $voucherInfo['email'],
            'phone'             => $voucherInfo['phone'],
            'payment_method'    => $order->payment_method,
            'product_name'      => $p->name,
            'product_summary'   => strip_tags($p->short_description),
            'product_img'       => $p->images[0]->src,
            'regular_price'     => $p->regular_price,
            'sale_price'        => $p->sale_price ? $p->sale_price : 'N/A',
            'discount'          => $p->sale_price ? round(round((($p->regular_price - $p->sale_price) / $p->regular_price) * 100, 0)) : 'N/A',
            'ordered_date'      => date('Y-m-d', strtotime($p->date_created)),
            'expiration_date'   => date('Y-m-d', strtotime("+30 days")),
        );
    }


    public function assembleEmail($voucherInfo){
        $p = $this->voucherData($voucherInfo);
        $note = "Thank you for choosing All-A. Please be reminded that this voucher is
        valid until ". date("F jS, Y", strtotime($p['expiration_date'])) . " only and shall not be extended.
        The vouchers are consider forfeited if it is not used within the validity period. Please
        be reminded also that voucher is non-refundable. <br> <br> Note: Upon redemption, Please
        set an appointment to the merchant or call the merchant. ". ($p['note'] ? $p['note'] : '');
        
        $template = $this->view->fetch('email-template.twig',
            array(
                'name'          => $p['product_name'],
                'customername'  => $p['user_name'],
                'summary'       => $p['product_summary'],
                'note'          => $note,
                'vouchercode'   => $p['code'],
                'saleprice'     => $p['sale_price'],
                'regularprice'  => $p['regular_price'],
                'discount'      => $p['sale_price'],
                'paymentmethod' => $p['payment_method'],
                'img'           => $p['product_img'],
                'orderdate'     => date("F j, Y", strtotime($p['ordered_date'])),
                'expirationdate'=> date("F j, Y", strtotime($p['expiration_date'])),
            )
        );
        return $template;
    }
    
}

?>