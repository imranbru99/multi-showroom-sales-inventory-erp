<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalarySetup extends Model {

    protected $table = "tbl_salary_setup";
    protected $fillable = [
        'company_id', 'showroom_id', 'title', 'cut_on', 'salary_cut', 'status',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
