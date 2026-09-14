<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankLoanPayment extends Model
{
	protected $table = "tbl_bank_loan_payments";

    protected $fillable = [
    	'showroom_id', 'loan_id', 'bank_name', 'loan_no', 'payment_date', 'paid_amount', 'created_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];

	public function payments()
	{
		return $this->hasMany(BankLoanSchedule::class, 'payment_id', 'id');
	}

	public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }
}
