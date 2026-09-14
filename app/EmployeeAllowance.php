<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model {

    protected $table = "tbl_employee_allowances";
    protected $fillable = [
        'company_id', 'showroom_id', 'staff_id', 'employee_designation', 'total_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function employeeAllowanceList() {
        return $this->hasMany(EmployeeAllowanceList::class, 'employee_allowance_id', 'id');
    }

}
