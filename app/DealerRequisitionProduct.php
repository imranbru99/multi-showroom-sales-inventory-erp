<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerRequisitionProduct extends Model {

    protected $table = "tbl_dealer_requisition_products";
    protected $fillable = [
        'company_id', 'approved_qty2', 'approved_amount2', 'suggested_qty', 'showroom_id', 'requisition_id', 'product_id', 'product_name', 'model_no', 'price', 'qty', 'amount', 'approved_qty', 'approved_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
