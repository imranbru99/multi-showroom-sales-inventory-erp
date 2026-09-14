<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveSetup;
use App\LeaveRequest;
use App\StaffSetup;
use DateTime;
use DB;
use PDF;
use MPDF;

class EmployeeLeaveLogController extends Controller {

    public function index(Request $request) {
        $title = "Employee Leave Log";
        $searchFormLink = 'employeeLeaveLog.index';
        $printFormLink = 'employeeLeaveLog.print';

        $staffId = $request->staff;

        $staffs = StaffSetup::where('company_id', $this->company)->where('status', 1)->cursor();

        $staffDetails = StaffSetup::find($staffId);

        $leaves = LeaveRequest::with('staff', 'leaveType')
                ->where('company_id', $this->company)
                ->where('employee_id', $staffId)
                ->groupBy('leave_type')
                ->get();

        return view('admin.employeeLeaveLog.index')->with(compact('title', 'staffs', 'searchFormLink', 'printFormLink', 'staffId', 'leaves', 'staffDetails'));
    }

    public function print(Request $request) {
        $title = "Employee Leave Log";

        $staffId = $request->staff;


        $staffDetails = StaffSetup::find($staffId);

        $leaves = LeaveRequest::with('staff', 'leaveType')
                ->where('company_id', $this->company)
                ->where('employee_id', $staffId)
                ->groupBy('leave_type')
                ->get();

        $pdf = PDF::loadView('admin.employeeLeaveLog.print', ['leaves' => $leaves, 'title' => $title, 'staffDetails' => $staffDetails]);

        return $pdf->stream('employee_leave_history_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
