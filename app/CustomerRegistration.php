<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistration extends Model {

    protected $table = "tbl_customers";
    protected $fillable = [
        'company_id', 'code', 'showroom_id', 'project_id', 'name', 'image', 'nick_name', 'nid', 'age', 'phone_no', 'marital_status', 'spouse_name', 'fathers_name', 'mothers_name', 'gender', 'current_residence', 'residence_duration', 'total_family_member', 'present_address', 'permanent_address', 'profession_name', 'profession_duration', 'total_earning_member', 'designation', 'monthly_income', 'work_place_address', 'status', 'is_audit', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function agreement() {
        return $this->hasOne(CustomerAgreement::class, 'customer_id', 'id');
    }

    public function project() {
        return $this->hasOne(ShowroomProjectSetup::class, 'id', 'project_id');
    }

}
