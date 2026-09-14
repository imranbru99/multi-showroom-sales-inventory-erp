<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCommissionAllocationList extends Model
{
    protected $guarded = [];

    public function allocation() {
        return $this->hasOne(EmployeeCommissionAllocation::class, 'id', 'allocation_id');
    }
    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'staff_id');
    }
}
