<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\StaffSetup;
use App\EmployeeSetup;
use App\ManualAttendance;
use App\ManualAttendanceList;
use App\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DailyAttendenceSheetController extends Controller {

    public function index(Request $request) {
        $title = "Daily Attendence Sheet";
        $searchFormLink = 'dailyAttendenceSheet.index';
        $printFormLink = 'dailyAttendenceSheet.print';

        $date = '';

        if ($request->date) {
            $date = date('d-m-Y', strtotime($request->date));
        }
        $attendences = ManualAttendance::with('attendenceList', 'attendenceList.staff')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('tbl_manual_attendance.date', $date)
                ->first();

        $data = [];

        if (!empty($attendences->attendenceList)) {
            foreach ($attendences->attendenceList as $key => $value) {
                $data[] = [
                    $value->employee_id
                ];
            }
        }

        $employeIds = StaffSetup::select('id')
                ->where('status', 1)
                ->whereNotIn('id', $data)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('id')
                ->toArray();

        $leaveDate = date('Y-m-d', strtotime($date));
        $leaves = LeaveRequest::with('staff', 'leaveType')
                ->whereIn('employee_id', $employeIds)
                ->where('leave_from_a', '<=', $leaveDate)
                ->where('leave_to_a', '>=', $leaveDate)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $leavesIds = LeaveRequest::select('employee_id')
                ->whereIn('employee_id', $employeIds)
                ->where('leave_from_a', '<=', $leaveDate)
                ->where('leave_to_a', '>=', $leaveDate)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('employee_id')
                ->toArray();

        $leave_presentIds = array_merge($data, $leavesIds);

        $absents = StaffSetup::where('status', 1)
                ->whereNotIn('id', $leave_presentIds)
                ->where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();


        return view('admin.dailyAttendenceSheet.index')->with(compact('title', 'attendences', 'searchFormLink', 'printFormLink', 'absents', 'leaves', 'date'));
    }

    public function print(Request $request) {
//        return $request;
        $title = "Daily Attendence Sheet";

        $date = date('d-m-Y', strtotime($request->date));

        $attendences = ManualAttendance::with('attendenceList', 'attendenceList.staff')
                ->where('tbl_manual_attendance.date', $date)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->first();

        $data = [];
        if (!empty($attendences->attendenceList)) {
            foreach ($attendences->attendenceList as $key => $value) {
                $data[] = [
                    $value->employee_id
                ];
            }
        }

        $employeIds = StaffSetup::select('id')
                ->where('status', 1)
                ->whereNotIn('id', $data)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('id')
                ->toArray();


        $leaves = LeaveRequest::with('staff', 'leaveType')
                ->whereIn('employee_id', $employeIds)
                ->where('leave_from_a', '<=', $date)
                ->where('leave_to_a', '>=', $date)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $leavesIds = LeaveRequest::select('employee_id')
                ->whereIn('employee_id', $employeIds)
                ->where('leave_from_a', '<=', $date)
                ->where('leave_to_a', '>=', $date)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('employee_id')
                ->toArray();

        $leave_presentIds = array_merge($data, $leavesIds);

        $absents = StaffSetup::where('status', 1)
                ->whereNotIn('id', $leave_presentIds)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.dailyAttendenceSheet.print', ['leaves' => $leaves, 'title' => $title, 'date' => $date, 'attendences' => $attendences, 'absents' => $absents]);

        return $pdf->stream('daily_attendence_sheet_' . $date . '.pdf');
    }

}
