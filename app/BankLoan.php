<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankLoan extends Model
{
	protected $table = "tbl_bank_loans";

    protected $fillable = [
    	'showroom_id','bank_name', 'loan_no', 'loan_date', 'loan_amount', 'total_amount', 'interest', 'total_installment', 'insatllment_amount', 'created_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];

	public function schedules()
	{
		return $this->hasMany(BankLoanSchedule::class, 'loan_id', 'id');
	}

	public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }
}
