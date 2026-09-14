<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionConfiguration extends Model
{
    protected $table = "tbl_commission_configuration";

    protected $fillable = [
    	'showroom_id','commission_type','dealer_id','staff_id','category_id','category_name','commission_rate','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
