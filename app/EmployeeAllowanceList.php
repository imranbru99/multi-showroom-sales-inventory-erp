<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowanceList extends Model {

    protected $table = "tbl_employee_allowance_lists";
    protected $fillable = [
        'company_id', 'showroom_id', 'employee_allowance_id', 'allowance_amount', 'allowance_name', 'allowance_type', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
