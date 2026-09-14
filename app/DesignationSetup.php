<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DesignationSetup extends Model
{
    protected $table = "tbl_designation";

    protected $fillable = [
        'showroom_id','name','official_title','description','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
