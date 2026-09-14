<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferProduct extends Model {

    protected $table = "tbl_transfer_products";
    protected $fillable = [
        'company_id', 'showroom_id', 'transfer_id', 'vendor_id', 'lifting_product_id', 'product_id', 'category_id', 'name', 'model_no', 'serial_no', 'color', 'qty', 'status', 'approve_by', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function transfer() {
        return $this->hasOne(Transfer::class, 'id', 'transfer_id');
    }

    public function lifting() {
        return $this->hasOne(LiftingProduct::class, 'id', 'lifting_product_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function receivedBy() {
        return $this->hasOne(Admin::class, 'id', 'approve_by');
    }

}
