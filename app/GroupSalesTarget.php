<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSalesTarget extends Model
{
	protected $table = "tbl_groups_sales_target";

	protected $fillable = [
		'company_id', 'showroom_id', 'group_id', 'year', 'month', 'date', 'total_target', 'total_target_amt', 'target_type', 'created_by', 'updated_by'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];
}
