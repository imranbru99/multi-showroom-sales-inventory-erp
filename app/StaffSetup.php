<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffSetup extends Model {

    protected $table = "tbl_staffs";
    protected $fillable = [
        'company_id', 'showroom_id', 'code', 'designation', 'name', 'short_name', 'contact', 'ac_no', 'ac_branch', 'address', 'email', 'national_id', 'joining_date', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function company() {
        return $this->hasOne(CompanySetup::class, 'id', 'company_id');
    }

}
