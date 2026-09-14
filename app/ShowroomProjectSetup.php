<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShowroomProjectSetup extends Model {

    protected $table = "tbl_showroomprojects";
    protected $fillable = [
        'company_id', 'code', 'showroom_id', 'name', 'address', 'remarks', 'status'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
