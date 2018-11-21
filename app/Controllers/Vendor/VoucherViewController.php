<?php
namespace App\Controllers\Vendor;

use App\Models\Voucher;
use App\Controllers\Controller;
use Slim\Views\Twig as View;

class VoucherViewController extends Controller{
    
    public function index($req, $res, $args){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($res, 'vendor/voucher-view.twig', ['voucher' => $this->voucherDetails($args['id'])] );
    }

    protected function voucherDetails($id){
        $result = Voucher::where('id', $id)->get();
        return $result;
    }
    
}