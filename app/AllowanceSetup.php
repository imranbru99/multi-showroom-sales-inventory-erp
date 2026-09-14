<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllowanceSetup extends Model {

    protected $table = "tbl_allowances";
    protected $fillable = [
        'company_id', 'showroom_id', 'code', 'title', 'type', 'remarks', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
