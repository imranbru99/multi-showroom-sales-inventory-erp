<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailSales extends Model {

    protected $table = "tbl_retails_sales";
    protected $fillable = [
        'company_id', 'sale_id', 'product_serial', 'product_id', 'product_model', 'cash_price', 'sales_price', 'mrp_price', 'hire_price', 'qty', 'warranty', 'remarks', 'discount', 'gift_voucher', 'exchange_crt', 'mobile_gift', 'gift_name', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function sale() {
        return $this->hasOne(RetailSale::class, 'id', 'sale_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
