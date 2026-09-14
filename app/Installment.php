<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model {

    protected $table = "tbl_installment";
    protected $fillable = [
        'company_id', 'showroom_id', 'customer_product_id', 'customer_id', 'product_id', 'invoice_no', 'installment_collector_id', 'installment_collector_name', 'customer_name', 'installment_price', 'booking_amount', 'installment_qty', 'installment_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function customer() {
        return $this->hasOne(CustomerRegistrationSetup::class, 'id', 'customer_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function collector() {
        return $this->hasOne(StaffSetup::class, 'id', 'installment_collector_id');
    }

    public function schedule() {
        return $this->hasMany(InstallmentSchedule::class, 'installment_id', 'id');
    }

    public function collections() {
        return $this->hasMany(InstallmentCollection::class, 'installment_id', 'id');
    }

}
