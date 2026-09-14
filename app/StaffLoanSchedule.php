<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffLoanSchedule extends Model
{
	protected $table = "tbl_staff_loan_schedules";

	protected $fillable = [
		'showroom_id', 'loan_id', 'date', 'amount', 'collection_amount', 'collection_id'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];


	public function loan()
	{
		return $this->hasOne(StaffLoan::class, 'id', 'loan_id');
	}
}
