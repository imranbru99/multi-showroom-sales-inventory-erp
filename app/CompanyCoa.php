<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyCoa extends Model {

    protected $table = "company_coa";
    protected $fillable = [
        'head_code', 'head_name', 'parent_head_name', 'head_level', 'head_type', 'transaction',  'general_ledger'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
