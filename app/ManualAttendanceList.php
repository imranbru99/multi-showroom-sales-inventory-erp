<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManualAttendanceList extends Model {

    protected $table = "tbl_manual_attendance_list";
    protected $fillable = [
        'company_id', 'showroom_id', 'manual_attendence_id', 'employee_id', 'in_time', 'out_time', 'date', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }

}
