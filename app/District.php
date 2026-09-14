<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = "tbl_districts";

    protected $fillable = [
        'name','bangla_name','status'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
