<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailSale extends Model {

    protected $table = "tbl_retails_sale";
    protected $guarded = [];

    public function products() {
        return $this->hasMany(RetailSales::class, 'sale_id', 'id');
    }

    public function installment() {
        return $this->hasOne(Installment::class, 'invoice_no', 'invoice_no');
    }

    public function guarantor() {
        return $this->hasMany(CustomerGuarantor::class, 'sale_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(CustomerRegistration::class, 'customer_id', 'id');
    }

    public function showroom() {
        return $this->hasOne(ShowroomSetup::class, 'id', 'showroom_id');
    }

    public function seller() {
        return $this->hasOne(StaffSetup::class, 'id', 'reference_id');
    }
    
    public function saleBy() {
        return $this->hasOne(StaffSetup::class, 'id', 'reference_id');
    }

    public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }

}
