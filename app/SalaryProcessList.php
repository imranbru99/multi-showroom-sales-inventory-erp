<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryProcessList extends Model {

    protected $table = "tbl_salary_process_list";
    protected $fillable = [
        'company_id', 'showroom_id', 'employee_id', 'salary_process_id', 'month_year','salary_amount', 'basic', 'house_rent',
        'medical', 'conveyance', 'children', 'ait', 'commission_payable', 'late_charge',
        'abcent_charge', 'total_payable', 'payment_date', 'payment_status', 'created_by',
    ];

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }
    
    public function salaryProcess() {
        return $this->hasOne(SalaryProcess::class, 'id', 'salary_process_id');
    }

}
