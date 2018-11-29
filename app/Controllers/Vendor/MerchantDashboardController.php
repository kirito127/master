<?php
namespace App\Controllers\Vendor;

//require 'Controller.php';
use App\Controllers\Controller;
use App\Models\Voucher;
use Slim\Views\Twig as View;
use Carbon\Carbon;
// use GuzzleHttp\Client;

class MerchantDashboardController extends Controller{
    
    public function index($request, $response){
        // $this->container->flash->addMessage('content-alert', 'cale');
        return $this->view->render($response, 'vendor/dashboard.twig', ['totalGross' => $this->getTotalGrossSales(), 'productsCount' => $this->getProductNumber()]);
    }

    protected function checkForDataDuplicate($arr){
        $aggregateArray = array();
        foreach($arr as $row){
                if(!array_key_exists($row['date'], $aggregateArray)){
                    if(isset( $aggregateArray[$row['date']])){
                        $aggregateArray[$row['date']] += $row['amount'];
                    }else{
                        $aggregateArray[$row['date']] = $row['amount'];    
                    }
                }else{
                    $aggregateArray[$row['date']] += $row['amount'];
                }
        }
        return $aggregateArray;
    }

    protected function getTotalGrossSales(){
        $amount = 0;
        $result = Voucher::select('regular_price', 'sale_price')->where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])->get();
        foreach($result as $price){
            $amount += ($price->sale_price != 0  ? $price->sale_price : $price->regular_price );
        }
        return number_format($amount) ?: '0';
        
    }

    protected function getProductNumber(){
        $result = $this->Api->getVendorProducts($_SESSION['userid']);
        return count($result);
    }

    public function loadGraph($req, $res, $args){
        $type   = $args['type'];
        $datefrom   = $args['datefrom'];
        $dateto     = $args['dateto'];

        try {
            $result;
            switch($type){
                case 'week': 
                    $result = $this->getGraphWeek();
                break;
                case 'month': 
                    $result = $this->getGraphMonth();
                break;
                case 'year': 
                    $result = $this->getGraphYear();
                break;
                case 'custom': 
                    $result = $this->getGraphCustom($datefrom , $dateto);
                break;
            }
            return json_encode($result);
            
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    protected function getGraphMonth(){
        $formattedData = array();
        $uniqueData = array();
        $finalData = array();

        //select all vouchers that vendor owns in same month and year
        $vouchers   =   Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])
                                ->whereYear('used_date',  '=', date('Y'))
                                ->whereMOnth('used_date', '=', date('m'))
                                ->orderBy('id','DESC')->get();

        //loop the result and format it from object to associative array ex. ['date'=>'Nov 1', 'amount'=>1200]
        foreach($vouchers as $voucher){
            $amount = ($voucher->sale_price != 0 ? $voucher->sale_price : $voucher->regular_price);
            $formattedData[] = array(
                'date'  => date("M d", strtotime($voucher->used_date)),
                'amount'=> $amount
            );
        }

        //check and sum for duplicate date, It converts to aggregate array ex. ['Nov 1':'1200']
        $uniqueData = $this->checkForDataDuplicate($formattedData);

        //j : The day of the month without leading zeros (1 to 31)
        $dayOfTheMonth = date('j'); 

        // loop and format it again to ass array, It fills all the empty date and give a 0 amount
        for($i=1;$i<=$dayOfTheMonth;$i++){
            $dayDate = date("M d", mktime(0, 0, 0, date('n'), $i));
            if(array_key_exists($dayDate, $uniqueData)){
                $finalData[] = array(
                    'date'  => $dayDate,'amount'=> $uniqueData[$dayDate]
                );
            }else{
                $finalData[] = array(
                    'date'  => $dayDate,'amount'=> 0
                );
            }
        }

        return $finalData;
    }

    protected function getGraphWeek(){
        $formattedData = array();
        $uniqueData = array();
        $finalData = array();

        $vouchers   =   Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])
                        ->whereYear('used_date', '=', date('Y'))
                        ->whereBetween('used_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->orderBy('id','DESC')->get();

        foreach($vouchers as $voucher){
            $amount = ($voucher->sale_price != 0 ? $voucher->sale_price : $voucher->regular_price);
            $formattedData[] = array(
                'date'  => date("l", strtotime($voucher->used_date)),
                'amount'=> $amount
            );
        }

        $uniqueData = $this->checkForDataDuplicate($formattedData);

        //N : The ISO-8601 numeric representation of a day (1 for Monday, 7 for Sunday)
        $dayOfTheWeekNum = date('N'); 

        for($i=1;$i<=$dayOfTheWeekNum;$i++){
            $daytext = date('l', strtotime("Sunday +{$i} days"));
            $finalData[] =  array_key_exists($daytext, $uniqueData) ? 
                            array('date' => $daytext, 'amount' => $uniqueData[$daytext]) : 
                            array('date' => $daytext, 'amount' => 0);
        }
        return $finalData;        
    }

    protected function getGraphYear(){
        $formattedData = array();
        $uniqueData = array();
        $finalData = array();

        $vouchers   =   Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])
                        ->whereYear('used_date', '=', date('Y'))
                        ->orderBy('id','DESC')->get();

        foreach($vouchers as $voucher){
            $amount = ($voucher->sale_price != 0 ? $voucher->sale_price : $voucher->regular_price);
            $formattedData[] = array(
                'date'  => date("M", strtotime($voucher->used_date)),
                'amount'=> $amount
            );
        }

        $uniqueData = $this->checkForDataDuplicate($formattedData);

         //n : gets the month by number
        $thisMonthNum = date('n');

        for($i=1; $i<=$thisMonthNum; $i++){
            $monthNum = date("M", mktime(0, 0, 0, $i, 10));

            if(array_key_exists($monthNum, $uniqueData)){
                $finalData[] = array(
                    'date' => $monthNum ,
                    'amount' => $uniqueData[$monthNum]
                );
            }else{
                $finalData[] = array(
                    'date' => $monthNum ,
                    'amount' => 0
                );
            }
        }
        return  $finalData;
    }

    protected function getGraphCustom($datefrom, $dateto){
        $formattedData = array();
        $uniqueData = array();
        $finalData = array();

        $vouchers   =   Voucher::where(['status' => 'Used', 'Vendor_Id' => $_SESSION['userid']])
                        ->whereDate('used_date', '>=', $datefrom)->whereDate('used_date', '<=', $dateto)
                        ->orderBy('id','DESC')->get();

        
        //loop the result and format it from object to associative array ex. ['date'=>'Nov 1', 'amount'=>1200]
        foreach($vouchers as $voucher){
            $amount = ($voucher->sale_price != 0 ? $voucher->sale_price : $voucher->regular_price);
            $formattedData[] = array(
                'date'  => date('M d, Y', strtotime($voucher->used_date)),
                'amount'=> $amount
            );
        }

        //check and sum for duplicate date, It converts to aggregate array ex. ['Nov 1':'1200']
        $uniqueData = $this->checkForDataDuplicate($formattedData);

        $dates = $this->Dagger->getDatesFromRange($datefrom,$dateto,'M d, Y');
        foreach($dates as $date){
            if(array_key_exists($date, $uniqueData)){
                $finalData[] = array(
                    'date' => $date, 'amount' => $uniqueData[$date]
                );
            }else{
                $finalData[] = array(
                    'date' => $date, 'amount' => 0
                );
            }
        }

        return $finalData;
    }



}