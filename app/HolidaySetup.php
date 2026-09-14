<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HolidaySetup extends Model {

    protected $table = "tbl_holidays";
    protected $fillable = [
        'company_id', 'showroom_id', 'from_date', 'to_date', 'name', 'type', 'description', 'days', 'holiday_for', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
