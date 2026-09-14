<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCommission extends Model
{
	protected $table = "tbl_employee_commissions";

    protected $fillable = [
    	'showroom_id','employee_id', 'commission', 'created_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];

	public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }
	public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }
}
