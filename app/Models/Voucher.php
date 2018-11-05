<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $fillable = [
        'Product_Id', 
        'User_Id', 
        'user_name', 
        'Order_Id',
        'Vendor_Id',
        'status',
        'resend',
        'code',
        'note',
        'email',
        'phone',
        'payment_method',
        'product_name',
        'product_summary',
        'product_img',
        'regular_price',
        'sale_price',
        'discount',
        'ordered_date',
        'expiration_date',
    ];
    public $timestamps = false;

}