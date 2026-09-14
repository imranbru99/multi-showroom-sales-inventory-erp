<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiftingReturn extends Model {

    protected $table = "tbl_lifting_returns";
    protected $fillable = [
        'company_id', 'vendor_id', 'product_type', 'showroom_id', 'store_or_showroom_type', 'store_or_showroom_id', 'product_id', 'serial_no', 'date', 'total_qty', 'total_price', 'total_mrp_price', 'total_haire_price', 'remarks', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
