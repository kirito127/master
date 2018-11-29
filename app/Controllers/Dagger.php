<?php
namespace App\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Controllers\Controller;
use Slim\Views\Twig as View;
class Dagger extends Controller{

    public function index(){
        return 'Hello world';
    }

    public function alertTemp($status, $content, $class = ''){
        $temp = "<div class='alert alert-$status alert-dismissible fade show $class' role='alert'>
                $content<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span></button></div>";
        return $temp;
    }

    public function datenow($date = null){
        return $date ? date("Y-m-d H:i:s", $date) : date("Y-m-d H:i:s") ;
    }

        //Copied the whole line of code, Damn me lol
    public function getDatesFromRange($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }
    
        return $array;
    }
}

