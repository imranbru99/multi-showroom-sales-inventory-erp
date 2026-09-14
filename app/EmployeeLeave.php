<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    protected $table = "tbl_employee_leave";

    protected $fillable = [
        'showroom_id','employee_id','leave_id','from_date','to_date','days','total_observe_leave_days','total_remain_leave_days','remarks','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
