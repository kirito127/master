<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Voucher;
use Slim\Views\Twig as View;
class VoucherController extends Controller{
    
    public function index($res, $req){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($req, 'admin/voucher.twig',[]);
    }

    public function loadVoucher($res, $req, $args){
        $vendorid   = $args['id'];
        $limit      = $args['limit'];
        $status     = $args['status'];
        $datefrom   = $args['datefrom'];
        $dateto     = $args['dateto'];
        $temp       = '';
        $sales        = 0;

        $result = Voucher::all()->take($limit);

        if( $vendorid <> 0){
            $result = $result->where('Vendor_Id', $vendorid);
        }

        if( $status <> 'All'){
            $result = $result->where('status', $status);
        }

        if($datefrom <> 0){ ////////////////////////used_date rather
            $result = $result->where('used_date', '>=', $datefrom);
        }
        
        if($dateto <> 0){
            $result = $result->where('used_date', '<=', $dateto);
        }



        if(empty($result)) return 'No records found';
        
        // $result = $vendorid == 0 ? Voucher::all()->where('status', $status)->take($limit) : Voucher::where('Vendor_Id', $vendorid)->where('status', $status)->take($limit);
        foreach($result as $voucher){
            $sales += ( $voucher->sale_price > 0 ? $voucher->sale_price : $voucher->regular_price );
            $badge;
            switch($voucher->status){
                case 'Used'     :   $badge = 'success'; break;
                case 'Unused'   :   $badge = 'warning'; break;
                case 'Expired'  :   $badge = 'danger'; break;
            }
            $temp .= "<tr style='cursor:pointer' class='animated fadeIn'>
                        <td>
                            <img class='img-avatar' style='max-width:40px;max-height:40px' src='". $voucher->product_img ."' alt=''>
                        </td>
                        <td>". $voucher->Order_Id ."</td>
                        <td>". $voucher->code." </td>
                        <td>". $voucher->product_name ."</td>
                        <td>
                            <span class='badge badge-$badge'>". $voucher->status ."</span>
                        </td>
                        <td>
                           P". ( $voucher->sale_price > 0 ? $voucher->sale_price : $voucher->regular_price ) ."
                        </td>
                        <td>". date('F j, Y', strtotime($voucher->ordered_date))."</td>
                        <td>". date('F j, Y', strtotime($voucher->send_date))."</td>
                        <td>". ( strtotime($voucher->used_date) != 0 ? date('F j, Y', strtotime($voucher->used_date)) : 'N/A')."</td>
                    </tr>";
        }
        return json_encode(array(
            'temp'  => $temp,
            'sales' => $sales,
        ));
    }

    public function getVendor(){

        try{
            
            $result = $this->Api->getVendors();
            $temp = '<option>All</option>';
            foreach($result as $vendor){
                $temp .= "<option value='". $vendor->id ."'> ". $vendor->display_name ."</option>";
            }
            return $temp;

        }catch(\Exception $e){
            return $e->getMessage();
        }

    }

    public function getOrder($res, $req, $args){
        try{
            $result = $this->container->Api->getOrders($args['ordernum']);
            $optiontemp ='<option> -- Select Product -- </option>';
            foreach($result->line_items as $items)  $optiontemp .="<option value='". $items->product_id ."'>". $items->name ."</option>";

            return json_encode(array(
                'temp'    => $optiontemp,
                'status'  => true
            ));
        }catch(\Exception $e){
            return json_encode(array(
                'temp'  => $this->Dagger->alertTemp('danger', '<strong>Oops!</strong> Order number is invalid, Please try again.'),
                'status'=> false
            ));
        }
    }

    public function sendEmail($res, $req, $args){
        try{
            $voucherInfo = array(
                'id'            => $args['voucherid'],
                'vouchercode'   => $args['vouchercode'],
                'note'          => isset($args['note']) ? $args['note'] : '',
                'email'         => base64_decode($args['email']),
                'ordernum'      => $args['ordernum'],
                'phone'         => isset($args['phone']) ? $args['phone'] : '',
            );
            $create = Voucher::insert($this->voucherData($voucherInfo));
            if($create){
                $sendEmail = $this->Email->sendMail($this->assembleEmail($voucherInfo), $voucherInfo['email']);
                if($sendEmail){
                    return json_encode(array(
                        'temp' => $this->Dagger->alertTemp('success', '<strong>Well done!</strong> Voucher successfully sent.'),
                        'status'=> true,
                    ));
                }else{
                    return json_encode(array(
                        'temp' => $this->Dagger->alertTemp('success', '<strong>Oops!</strong> '. $sendEmail),
                        'status'=> false,
                    ));
                }
            }else{
                return json_encode(array(
                    'temp' => $this->Dagger->alertTemp('success', '<strong>Oops!</strong> '. $create),
                    'status'=> false,
                ));
            }
        }catch(\Exception $e){
            return $this->Dagger->alertTemp('danger', "<strong>Oops!</strong> ". $e->getMessage());
        }
    }

    //get voucher data 
    protected function voucherData($voucherInfo){ 
        $p = $this->Api->getProductById($voucherInfo['id']);
        $order = $this->container->Api->getOrders($voucherInfo['ordernum']);

        return array(
            'Product_Id'        => $voucherInfo['id'],
            'User_Id'           => $order->customer_id,
            'user_name'         => $order->billing->first_name ? $order->billing->first_name : $order->shipping->first_name,
            'Order_Id'          => $voucherInfo['ordernum'],
            'Vendor_Id'         => $p->vendor,
            'code'              => $voucherInfo['vouchercode'],
            'note'              => ( $voucherInfo['note'] ?: ''),
            'email'             => $voucherInfo['email'],
            'phone'             => ( $voucherInfo['phone'] ?: ''),
            'payment_method'    => $order->payment_method,
            'product_name'      => $p->name,
            'product_summary'   => $p->short_description,
            'product_img'       => $p->images[0]->src,
            'regular_price'     => $p->regular_price,
            'sale_price'        => $p->sale_price ? $p->sale_price : 'N/A',
            'discount'          => $p->sale_price ? round(($p->sale_price / $p->regular_price) * 100) : 'N/A',
            // 'discount'          => $p->sale_price ? round(round((($p->regular_price - $p->sale_price) / $p->regular_price) * 100, 0)) : 'N/A',
            'ordered_date'      => date('Y-m-d', strtotime($p->date_created)),
            'expiration_date'   => date('Y-m-d', strtotime("+30 days")),
        );
    }

    //return an email template
    public function assembleEmail($voucherInfo){
        $p = $this->voucherData($voucherInfo);
        $note = "Thank you for choosing All-A. Please be reminded that this voucher is
        valid until ". date("F j, Y", strtotime($p['expiration_date'])) . " only and shall not be extended.
        The vouchers are consider forfeited if it is not used within the validity period. Please
        be reminded also that voucher is non-refundable. <br> <br> Note: Voucher is transferable and may be given as a gift to family and friends. Upon redemption, Please
        set an appointment to the merchant or call the merchant. ". ($p['note'] ? $p['note'] : '');
        
        $template = $this->view->fetch('email-template.twig',
            array(
                'name'          => $p['product_name'],
                'customername'  => $p['user_name'],
                'summary'       => strip_tags($p['product_summary']),
                'note'          => $note,
                'vouchercode'   => $p['code'],
                'saleprice'     => '₱'.$p['sale_price'] .'.00',
                'regularprice'  => '₱'.$p['regular_price'] .'.00',
                'discount'      => $p['discount'] .'%',
                'paymentmethod' => $p['payment_method'],
                'img'           => $p['product_img'],
                'orderdate'     => date("F j, Y", strtotime($p['ordered_date'])),
                'expirationdate'=> date("F j, Y", strtotime($p['expiration_date'])),
            )
        );
        return $template;
    }
}