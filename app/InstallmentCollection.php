<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallmentCollection extends Model {

    protected $table = "tbl_installment_collection";
    protected $fillable = [
        'company_id', 'reference_id', 'employee_id', 'showroom_id', 'installment_id', 'customer_product_id', 'customer_id', 'product_id', 'invoice_no', 'customer_name', 'installment_price', 'booking_amount', 'installment_qty', 'installment_amount', 'status', 'created_by', 'updated_by'
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

    public function sale() {
        return $this->hasOne(RetailSale::class, 'invoice_no', 'invoice_no');
    }

    public function collector() {
        return $this->hasOne(StaffSetup::class, 'id', 'reference_id');
    }

    public function collection() {
        return $this->hasOne(InstallmentCollectionList::class, 'installment_collection_id', 'id');
    }

    public function collections() {
        return $this->hasMany(InstallmentCollectionList::class, 'installment_collection_id', 'id');
    }

}
