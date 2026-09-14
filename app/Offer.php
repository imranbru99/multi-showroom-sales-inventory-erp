<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = "tbl_offer";

    protected $fillable = ['offerName','start_date','end_date','offer_type','amount','perAmount','product_id', 'status'];

	protected $hidden = [
		'created_at','updated_at'
	];
}
