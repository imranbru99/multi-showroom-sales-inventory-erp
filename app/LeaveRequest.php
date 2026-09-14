<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model {

    protected $table = "tbl_leave_requests";
    protected $fillable = [
        'company_id', 'showroom_id', 'employee_id', 'leave_type', 'leave_from', 'leave_to',
        'leave_from_a', 'leave_to_a', 'duration_a', 'leave_day_a', 'approve_by', 'approve_by_a',
        'duration', 'leave_day', 'remarks', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }

    public function leaveType() {
        return $this->hasOne(LeaveSetup::class, 'id', 'leave_type');
    }

}
