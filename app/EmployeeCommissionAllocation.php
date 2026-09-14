<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCommissionAllocation extends Model
{
    protected $guarded = [];

    public function allocations() {
        return $this->hasMany(EmployeeCommissionAllocationList::class, 'allocation_id', 'id');
    }
}
