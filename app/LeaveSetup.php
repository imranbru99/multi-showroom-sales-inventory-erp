<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveSetup extends Model
{
    protected $table = "tbl_leaves";

    protected $fillable = [
        'showroom_id','name','type','days','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
