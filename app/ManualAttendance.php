<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManualAttendance extends Model {

    protected $table = "tbl_manual_attendance";
    protected $fillable = [
        'company_id', 'showroom_id', 'date', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function attendenceList() {
        return $this->hasMany(ManualAttendanceList::class, 'manual_attendence_id', 'id');
    }

}
