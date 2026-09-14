<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = "tbl_product_images";

    protected $fillable = [
    	'product_id','image'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
