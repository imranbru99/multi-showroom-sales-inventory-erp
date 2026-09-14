<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailSalesReturnProducts extends Model {

    protected $table = "retail_sale_return_products";
    protected $guarded = [];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function return() {
        return $this->hasOne(RetailSalesReturn::class, 'id', 'return_id');
    }

}
