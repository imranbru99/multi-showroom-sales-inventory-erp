<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceSpareAllocation extends Model {

    protected $table = "tbl_service_allocation_spare_products";
    protected $fillable = [
        'company_id', 'showroom_id', 'service_allocation_id', 'invoice_no', 'product_id', 'model_no', 'serial_no', 'qty', 'sale_price', 'created_by',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
