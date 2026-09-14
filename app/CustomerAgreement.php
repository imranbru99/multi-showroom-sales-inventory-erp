<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAgreement extends Model {

    protected $table = "customer_agreement";
    protected $guarded = [];

    public function customer() {
        return $this->hasOne(CustomerRegistrationSetup::class, 'id', 'customer_id');
    }

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }

}
