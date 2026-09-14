<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashCollection extends Model
{
    protected $table = "tbl_cash_collection";

    protected $fillable = [
		'showroom_id','invoice_id','collector_id','collector_name','collection_no','invoice_amount','previous_collection','collection_date','collection_amount','current_due','remarks','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
