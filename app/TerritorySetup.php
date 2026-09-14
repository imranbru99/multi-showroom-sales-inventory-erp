<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TerritorySetup extends Model
{
	protected $table = "tbl_territories";

    protected $fillable = [
    	'company_id', 'showroom_id','area_id','code','name','incharge_name','address','contact','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
