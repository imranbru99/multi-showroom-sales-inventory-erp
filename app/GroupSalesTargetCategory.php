<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSalesTargetCategory extends Model
{
	protected $table = "tbl_groups_sales_target_category";

    protected $fillable = [
    	'group_sales_target_id','category_id','target', 'target_amt'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
