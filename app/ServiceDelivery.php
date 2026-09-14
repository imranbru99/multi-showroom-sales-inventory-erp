<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceDelivery extends Model {

    protected $table = "tbl_service_delivery";
    protected $fillable = [
        'company_id', 'showroom_id', 'invoice_no', 'service_allocation_id', 'delivery_issue_to', 'delivery_date', 'service_amount', 'delivery_condition', 'created_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function serviceAllocation() {
        return $this->hasOne(ServiceAllocation::class, 'id', 'service_allocation_id');
    }

    public function spareProducts() {
        return $this->hasMany(ServiceSpareAllocation::class, 'service_allocation_id', 'service_allocation_id');
    }

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }

}
