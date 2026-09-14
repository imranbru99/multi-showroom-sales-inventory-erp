<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourierSetup extends Model {

    protected $table = "tbl_couriers";
    protected $fillable = [
        'company_id', 'showroom_id', 'code', 'name', 'phone', 'address', 'status'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
