<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffLoan extends Model
{
	protected $table = "tbl_staff_loans";

	protected $fillable = [
		'showroom_id', 'staff_id', 'date', 'type', 'remarks', 'total_installment', 'amount'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];


	public function staff()
	{
		return $this->hasOne(StaffSetup::class, 'id', 'staff_id');
	}

	public function schedules()
	{
		return $this->hasMany(StaffLoanSchedule::class, 'loan_id', 'id');
	}
}
