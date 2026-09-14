<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionRequisition extends Model {

    protected $table = "tbl_production_requisitions";
    protected $fillable = [
        'company_id', 'showroom_id', 'requisition_no', 'requisitions_name_id', 'date', 'product_id', 'total_qty', 'approved_by', 'total_approve_qty', 'status', 'suggested', 'created_by', 'updated_by', 'total_qty2', 'final_status', 'approved_by2',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function requisitions() {
        return $this->hasMany(ProductionRequisitonInfo::class, 'requisition_id', 'id');
    }

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'requisitions_name_id');
    }

    public function user() {
        return $this->hasOne(UserRoles::class, 'id', 'approved_by');
    }

}
