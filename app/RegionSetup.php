<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegionSetup extends Model {

    protected $table = "tbl_region";
    protected $fillable = [
        'company_id', 'showroom_id', 'code', 'name', 'incharge_name', 'address', 'contact', 'email', 'status'
    ];

}
