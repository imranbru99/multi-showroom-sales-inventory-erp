<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceProductReceive extends Model {

    protected $table = "tbl_service_product_receive";
    protected $fillable = [
        'company_id', 'showroom_id', 'invoice_no', 'receive_date', 'product_id', 'product_model', 'product_serial', 'dealer_id', 'sale_date', 'type', 'cn_no', 'problem_list', 'product_condition', 'qty', 'created_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function remarks() {
        return $this->hasOne(ServiceProductReceiveRemarks::class, 'service_receive_id', 'id');
    }
    
    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }
    
    public function lifting() {
        return $this->hasOne(LiftingProduct::class, 'serial_no', 'product_serial');
    }
    
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
