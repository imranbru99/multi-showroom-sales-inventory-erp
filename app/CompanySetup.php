<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySetup extends Model {

    protected $table = "tbl_company";
    protected $guarded = [];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
