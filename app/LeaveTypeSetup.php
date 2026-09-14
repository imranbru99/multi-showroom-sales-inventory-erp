<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveTypeSetup extends Model {

    protected $table = "tbl_leaves";
    protected $fillable = [
        'company_id', 'showroom_id', 'name', 'days', 'status', 'created_by', 'updated_by'
    ];

}
