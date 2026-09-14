<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAdvance extends Model
{
    protected $table = "tbl_product_advance";

    protected $fillable = [
    	'product_id','product_section','related_product_id','pre_order_duration','shipping','hot_discount','hot_discount_date','special_discount','special_discount_date'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
