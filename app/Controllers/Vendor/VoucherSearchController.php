<?php
namespace App\Controllers\Vendor;

use App\Models\Voucher;
use App\Controllers\Controller;
use Slim\Views\Twig as View;
// use GuzzleHttp\Client;

class VoucherSearchController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/voucher-search.twig');
    }

    public function updateVoucher($req, $res, $args){
        try {
            $voucher = Voucher::where('id', $args['id'])->update(array('status' => 'Used')); //update from db
            return $voucher ?   $this->Dagger->alertTemp('success', '<strong>Well done ! </strong> Voucher successfully updated.') :
                                $this->Dagger->alertTemp('success', '<strong>Oops ! </strong> Some error occured, Please try again.') ;
        } catch (\Exception $e) {
            return $this->Dagger->alertTemp('danger', '<strong>Error </strong>: '.$e->getMessage());
        }
    }

    public function searchVoucher($req, $res, $args){
        try {
            $voucher = Voucher::where('code', $args['code'])->get()->first(); //select from db
            return $voucher ? 
                            $this->TemplateVoucher($voucher) : 
                            $this->Dagger->alertTemp('danger', '<strong>No voucher found ! </strong> Please check and try again.');
                            
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    // Assemble the voucher template and return a html markup
    protected function TemplateVoucher($voucher){
        $template;
        if($voucher){
            $badge = $voucher->status == 'Unused' ? "success" : 'danger';
            $badgeLabel = ($voucher->status == 'Unused' ? "Available" : ($voucher->status == 'Used' ? 'Not Available' : 'Expired'));
            $btnStatus = $voucher->status == 'Unused' ? "id='confirmbtn'" : 'disabled';
            $template = "<div class='row rounded my-2'>
                                <input type='hidden' id='id' value='". $voucher->id . "'>
                                <div class='col-md-3 p-0 bg-white'>
                                    <img class='img-fluid' src='".  $voucher->product_img ."' alt='Card image cap'>
                                </div>
                                <div class='col-md-9 p-0 bg-white'>
                                    <ul class='list-group'>
                                        <li class='list-group-item rounded-0'>
                                            <span class='badge badge-$badge badge-pill float-right'>$badgeLabel</span>
                                            <h5 class='text-dark'>". $voucher->product_name ."</h5>
                                            <small>". $voucher->product_summary ."</small>
                                        </li>
                                        <li class='list-group-item rounded-0'>Voucher Code : ". $voucher->code ."</li>
                                        <li class='list-group-item rounded-0'>Sale Price : ₱". ($voucher->sale_price ? number_format($voucher->sale_price, 2) : 'N/A') ."</li>
                                        <li class='list-group-item rounded-0'>Regular Price : ₱". number_format($voucher->regular_price, 2) ."</li>
                                        <li class='list-group-item rounded-0'>Expiration Date : ".  date('F j, Y', strtotime($voucher->expiration_date)) . "</li>
                                    </ul>
                                </div>
                                <div class='col-12 text-center pt-3'>
                                    <button id='closebtn' class='btn btn-outline-danger btn-radius'><i class='fas fa-times'></i> Close</button>
                                    <button $btnStatus class='btn btn-success btn-radius'><i class='fas fa-check'></i> Confirm</button>
                                </div>
                        </div>";
        }
        return $template;
    }
    
}