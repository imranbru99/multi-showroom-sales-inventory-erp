<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManufactureSetupList extends Model {

    protected $table = "tbl_manufacture_lists";
    protected $fillable = [
        'company_id', 'showroom_id', 'manufacture_id', 'parts_id', 'parts_amount', 'required_unit', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
