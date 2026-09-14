<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailSalesReport extends Model
{
    protected $table = "tbl_previous_retail_sales";

    protected $guarded = [];

	protected $hidden = [
		'created_at','updated_at'
	];

	public $timestamps = false;

}
