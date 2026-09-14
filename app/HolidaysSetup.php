<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HolidaysSetup extends Model
{
    protected $table = "tbl_holidays";

    protected $fillable = [
        'showroom_id','from_date','to_date','name','type','status','created_by','updated_by'
    ];

	
}
