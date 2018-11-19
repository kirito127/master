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

    public function loadVoucher($res, $req, $args){
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
        return $vouchers ? $this->TemplateRow($vouchers->get()) : 'Empty';
    }

    protected function TemplateRow($vouchers){
        $template = '';
        foreach($vouchers as $voucher){
            $price = $voucher->sale_price != 0 ? $voucher->sale_price  : $voucher->regular_price; // I wrote it here because I care for you !
            $template .= "<tr class='animated fadeIn'>
                            <td>". $voucher->code ." </td>
                            <td>". $voucher->product_name."</td>
                            <td>P". number_format($price, 2)."</td>
                            <td>". $voucher->email ."</td>
                            <td>". date('M. j, Y h:i A', strtotime($voucher->used_date)) ."</td>
                        </tr>";
        }
        return $template;
    }
    
}