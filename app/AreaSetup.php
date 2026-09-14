<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaSetup extends Model {

    protected $table = "tbl_area";
    protected $fillable = [
        'company_id', 'showroom_id', 'region_id', 'code', 'name', 'incharge_name', 'address', 'contact', 'email', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
