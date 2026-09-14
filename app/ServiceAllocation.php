<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceAllocation extends Model {

    protected $table = "tbl_service_allocation";
    protected $fillable = [
        'company_id', 'showroom_id', 'service_receive_id', 'invoice_no', 'issue_date', 'finish_date', 'product_id', 'product_model', 'product_serial', 'qty', 'employee_id', 'spare_products', 'remarks', 'status', 'created_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function remarks() {
        return $this->hasMany(ServiceProductReceiveRemarks::class, 'job_allocation_id', 'id');
    }

    public function serviceProduct() {
        return $this->hasOne(ServiceProductReceive::class, 'id', 'service_receive_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    
    public function spareProducts() {
        return $this->hasMany(ServiceSpareAllocation::class, 'service_allocation_id', 'id');
    }

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }

}
