<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankLoanSchedule extends Model
{
	protected $table = "tbl_bank_loan_schedules";

	protected $fillable = [
		'showroom_id', 'loan_id', 'date', 'amount', 'paid_amount', 'payment_date', 'payment_id'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];


	public function loan()
	{
		return $this->hasOne(BankLoan::class, 'id', 'loan_id');
	}
}
