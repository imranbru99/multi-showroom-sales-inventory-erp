<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSetup extends Model
{
    protected $table = "tbl_employee";

    protected $fillable = [
        'showroom_id','designation_id','employee_no','joining_date','resigning_date','name','mobile','email','image','resigning_reason','resigned','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
