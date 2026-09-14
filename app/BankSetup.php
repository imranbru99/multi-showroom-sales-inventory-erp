<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankSetup extends Model
{
	protected $table = "tbl_bank";

    protected $fillable = [
    	'code','name','phone','address','status'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
