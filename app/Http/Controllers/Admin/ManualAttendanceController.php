<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\StaffSetup;
use App\EmployeeSetup;
use App\ManualAttendance;
use App\ManualAttendanceList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManualAttendanceController extends Controller {

    public function index() {
        $title = "Manual Attendance";

        $manualAttendences = ManualAttendance::with('attendenceList', 'attendenceList.staff')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        return view('admin.manualAttendance.index')->with(compact('title', 'manualAttendences'));
    }

    public function add() {
        $title = "Add Manual Attendence";
        $formLink = "manualAttendance.save";
        $buttonName = "Save";

        $employees = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();


        return view('admin.manualAttendance.add')->with(compact('title', 'formLink', 'buttonName', 'employees'));
    }

    public function save(Request $request) {
        $request->validate([
            'date' => 'required|unique:tbl_manual_attendance'
        ]);

        $date = date('d-m-Y', strtotime($request->date));

        $attendence = ManualAttendance::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'date' => $date,
                    'created_by' => $this->userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
        ]);


        $count = count($request->employeeId);
        if ($request->employeeId) {
            $postData = [];
            for ($i = 0; $i < $count; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'manual_attendence_id' => $attendence->id,
                    'employee_id' => $request->employeeId[$i],
                    'in_time' => $request->in_time[$i],
                    'date' => $date,
                    'created_by' => $this->userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            ManualAttendanceList::insert($postData);
        }


        return redirect(route('manualAttendance.index'))->with('msg', 'Manual Attendence Added Successfully');
    }

    public function edit($id) {
        $title = "Edit Manual Attendence";
        $formLink = "manualAttendance.update";
        $buttonName = "Update";


        $attendEmployees = ManualAttendance::with('attendenceList', 'attendenceList.staff')->where('id', $id)->first();
        $data = [];
        foreach ($attendEmployees->attendenceList as $key => $value) {
            $data[] = [
                $value->employee_id
            ];
        }

        $employees = StaffSetup::where('status', 1)
                ->whereNotIn('id', $data)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.manualAttendance.edit')->with(compact('title', 'formLink', 'buttonName', 'employees', 'attendEmployees'));
    }

    public function update(Request $request) {

        $date = date('d-m-Y', strtotime($request->date));

        $attendence = ManualAttendance::find($request->id);

        $attendence->update([
            'date' => $date
        ]);

        ManualAttendanceList::where('manual_attendence_id', $attendence->id)->delete();

        $count = count($request->employeeId);
        if ($request->employeeId) {
            $postData = [];
            for ($i = 0; $i < $count; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'manual_attendence_id' => $attendence->id,
                    'employee_id' => $request->employeeId[$i],
                    'in_time' => $request->in_time[$i],
                    'date' => $date,
                    'created_by' => $this->userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            ManualAttendanceList::insert($postData);
        }


        return redirect(route('manualAttendance.index'))->with('msg', 'Manual Attendence Updated Successfully');
    }

    public function delete(Request $request) {
        ManualAttendanceList::where('manual_attendence_id', $request->attendanceId)->delete();
        ManualAttendance::where('id', $request->attendanceId)->delete();
    }

}
