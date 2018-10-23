<?php
namespace App\Controllers;

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

    public function datenow(){
        return date("Y-m-d H:i:s");
    }
}

