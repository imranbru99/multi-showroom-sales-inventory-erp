<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionRequisitonInfo extends Model {

    protected $table = "tbl_production_requisition_info";
    protected $fillable = [
        'company_id', 'showroom_id', 'requisition_id', 'product_id', 'product_name', 'model_no', 'qty', 'approved_qty', 'approved_by', 'approved_by2', 'approved_qty2', 'final_status', 'suggested_qty', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
