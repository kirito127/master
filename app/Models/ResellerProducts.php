<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerProducts extends Model
{
    protected $table = 'reseller_products';
    protected $fillable = [
        'Reseller_Id', 
        'name', 
        'code', 
        'status'
    ];
    
    public $timestamps = false;

}