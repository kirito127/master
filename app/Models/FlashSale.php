<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $table = 'flash_sale';
    protected $fillable = [
        'Product_Id', 
        'User_Id', 
        'status', 
        'start_date',
        'end_date',
        'sale_price',
        'custom_sale_price',
        'set_date',
    ];
    public $timestamps = false;

    
    // static function index(){
    //     $flashsale = FlashSale::all();
    //     return $flashsale;
    // }
}