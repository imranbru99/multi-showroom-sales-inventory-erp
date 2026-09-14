<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSetup extends Model
{
	protected $table = "tbl_stores";

	protected $fillable = [
		'company_id', 'showroom_id', 'code', 'type', 'name', 'address', 'remarks', 'status'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];
}
