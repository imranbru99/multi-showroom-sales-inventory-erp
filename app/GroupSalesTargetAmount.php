<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSalesTargetAmount extends Model {

    protected $table = "tbl_groups_sales_target_amount";
    protected $fillable = [
        'showroom_id', 'group_sales_target_id', 'staff_id', 'target_amount', 'created_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    
    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'staff_id');
    }

}
