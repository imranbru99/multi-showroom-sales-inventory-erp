<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionIssue extends Model {

    protected $table = "tbl_production_issue";
    protected $fillable = [
        'company_id', 'showroom_id', 'serial_no', 'requisition_id', 'requisitions_name_id', 'date', 'total_qty', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'requisitions_name_id');
    }

}
