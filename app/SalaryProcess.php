<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryProcess extends Model {

    protected $table = "tbl_salary_process";
    protected $fillable = [
        'showroom_id', 'month_year', 'company_name', 'company_id', 'created_by'
    ];

    public function salaryProcessList() {
        return $this->hasMany(SalaryProcessList::class, 'salary_process_id', 'id');
    }
    
    public function showroom() {
        return $this->hasOne(ShowroomSetup::class, 'id', 'showroom_id');
    }

}
