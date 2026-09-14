<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductExchange extends Model
{
    protected $table = "tbl_product_exchanges";
    protected $guarded = [];


    public function sale()
    {
        return $this->hasOne(RetailSale::class, 'id', 'sale_id');
    }
    public function customer()
    {
        return $this->hasOne(CustomerRegistrationSetup::class, 'id', 'customer_id');
    }

    public function return()
    {
        return $this->hasOne(RetailSalesReturn::class, 'id', 'return_id');
    }
}
