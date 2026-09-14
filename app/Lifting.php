<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lifting extends Model {

    protected $table = "tbl_liftings";
    protected $fillable = [
        'company_id', 'showroom_id', 'product_type', 'serial_no', 'vaouchar_no', 'vendor_id', 'store_or_showroom_type', 'store_or_showroom_id', 'purchase_by', 'submission_date', 'vouchar_date', 'total_qty', 'total_price', 'total_mrp_price', 'total_haire_price', 'discount', 'vat', 'net_amount', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function vendor() {
        return $this->hasOne('App\VendorSetup', 'id', 'vendor_id');
    }

}
