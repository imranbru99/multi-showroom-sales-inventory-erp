<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitEntry extends Model
{
    protected $table = "tbl_account_transactions";

    protected $fillable = [
        'voucher_no','voucher_type','voucher_date','coa_id','coa_head_code','showroom_id','narration','debit_amount','credit_amount','posted','approve','active','delete','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
