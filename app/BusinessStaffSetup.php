<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessStaffSetup extends Model
{
	protected $table = "tbl_business_staffs";

    protected $fillable = [
    	'code','name','contact','address','email','national_id','joining_date','status'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];	
}
