<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailSalesReturn extends Model {

    protected $table = "retail_sale_return";
    protected $guarded = [];

    public function customer() {
        return $this->hasOne(CustomerRegistrationSetup::class, 'id', 'customer_id');
    }

    public function products() {
        return $this->hasMany(RetailSalesReturnProducts::class, 'return_id', 'id');
    }

}
