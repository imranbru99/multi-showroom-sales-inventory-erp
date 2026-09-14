<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerRequisition extends Model {

    protected $table = "tbl_dealer_requisitions";
    protected $fillable = [
        'company_id', 'suggested', 'showroom_id', 'dealer_id', 'requisition_no', 'date', 'product_id', 'total_qty', 'total_amount', 'approved_by', 'total_approve_qty', 'total_approve_amount', 'status', 'created_by', 'updated_by', 'total_qty2', 'total_amount2', 'final_status', 'approved_by2',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function requisitions() {
        return $this->hasMany(DealerRequisitionProduct::class, 'requisition_id', 'id');
    }

    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

    public function user() {
        return $this->hasOne(UserRoles::class, 'id', 'approved_by');
    }

}
