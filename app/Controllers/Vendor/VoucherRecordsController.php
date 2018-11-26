<?php
namespace App\Controllers\Vendor;

use App\Models\Voucher;
use App\Controllers\Controller;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class VoucherRecordsController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/voucher-records.twig');
    }

    public function viewVoucherMobile($req, $res, $args){
        try {
            return $res->withRedirect($this->router->pathFor('vendor.voucher-view', ['id' => $args['id']]));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadVoucherMobile($req, $res, $args){
        $limit      =   $args['limit'];
        $filter     =   $args['filter'];
        $datefrom   =   $args['datefrom'];
        $dateto     =   $args['dateto'];

        $vouchers = Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])->take($limit);
        
        if($filter){
            $vouchers = $vouchers->Where('code', $filter);
        }

        if($datefrom <> 0 && $dateto <> 0){
            $vouchers = $vouchers->whereDate('used_date', '>=', $datefrom)->whereDate('used_date', '<=', $dateto);
        }elseif($datefrom <> 0){
            $vouchers = $vouchers->whereDate('used_date', '>=', $datefrom);
        }elseif($dateto <> 0){
            $vouchers = $vouchers->whereDate('used_date', '<=', $dateto);
        }

        return $vouchers ? $this->templateRowMobile($vouchers->get()) : 'Empty';
    }

    protected function templateRowMobile($vouchers){
        $template = '';
        foreach($vouchers as $voucher){

            $price = '';
            if($voucher->sale_price != 0){
                $price = 'P'. number_format($voucher->sale_price);
            }else{
               $price = $voucher->regular_price ? 'P'. number_format($voucher->regular_price): 'Variation Product';
            }

            $template .="<div id='" .$voucher->id. "' class='card mb-1'>
                            <div class='card-body px-2 py-3'>
                            <div class='row'>
                                <div class='col-sm-8 col-8'>
                                <h6 class='text-dark mb-0'>". $voucher->product_name."</h6>
                                <small class='card-text'>". $voucher->code ."</small>
                                </div>
                                <div class='col-sm-4 col-4 d-flex align-items-center justify-content-end'>
                                <h5 class='mb-0'>
                                    <span class='badge badge-primary p-2'>$price</span>
                                </h5>
                                </div>
                            </div>
                            </div>
                        </div>";
        }
        return $template;      
    }

    public function loadVoucher($req, $res, $args){
        $limit      =   $args['limit'];
        $filter     =   $args['filter'];
        $datefrom   =   $args['datefrom'];
        $dateto     =   $args['dateto'];

        $vouchers = Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])->take($limit);

        if($filter){
            $vouchers = $vouchers->Where('code', $filter);
            //->where('product_name', 'LIKE',  "%$filter%")
        }

        if($datefrom <> 0 && $dateto <> 0){
          // $vouchers = $vouchers->whereBetween('used_date', [$datefrom, $dateto]);
            $vouchers = $vouchers->whereDate('used_date', '>=', $datefrom)->whereDate('used_date', '<=', $dateto);
        }elseif($datefrom <> 0){
            $vouchers = $vouchers->whereDate('used_date', '>=', $datefrom);
        }elseif($dateto <> 0){
            $vouchers = $vouchers->whereDate('used_date', '<=', $dateto);
        }
       
        //return $vouchers->toSql();
        return $vouchers ?  json_encode($this->templateRow($vouchers->get())) : 'Empty';
    }

    protected function templateRow($vouchers){
        $template = '';
        $amount = 0;
        foreach($vouchers as $voucher){
            $price = $voucher->sale_price != 0 ? $voucher->sale_price  : $voucher->regular_price; // I wrote it here because I care for you !
            $amount += $price;
            $template .= "<tr style='cursor:pointer;' id='" .$voucher->id. "' class='animated fadeIn'>
                            <td>". $voucher->code ." </td>
                            <td>". $voucher->product_name."</td>
                            <td>P". number_format($price, 2)."</td>
                            <td>". $voucher->email ."</td>
                            <td>". date('M. j, Y h:i A', strtotime($voucher->used_date)) ."</td>
                        </tr>";
        }
        return array('template'=> $template, 'amount' => $amount);
    }
    
}