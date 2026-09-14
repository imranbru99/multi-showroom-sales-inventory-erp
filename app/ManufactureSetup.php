<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManufactureSetup extends Model {

    protected $table = "tbl_manufacture";
    protected $fillable = [
        'company_id', 'showroom_id', 'product_id', 'catgorie_id', 'product_unit', 'total_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
