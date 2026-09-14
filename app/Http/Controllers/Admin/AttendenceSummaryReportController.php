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

class AttendenceSummaryReportController extends Controller {

    public function index(Request $request) {
        $title = "Attendence Summarry";
        $searchFormLink = 'attendenceSummary.index';
        $printFormLink = 'attendenceSummary.print';

        $monthYear = $request->month;

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);



        $monthly_attendance = [];
        $staffSetups = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        foreach ($staffSetups as $staffSetup) {

            $monthly_staff = [];
            for ($i = 1; $i <= $num_of_days; $i++) {
                $date = str_pad($i, 2, '0', STR_PAD_LEFT);
                $ind_date = $date . "-" . $month . "-" . $year;
                $monthly_staff[] = $this->day_attendance($staffSetup->id, $ind_date);
            }

            $monthly_attendance[] = [
                'monthly_staff' => $monthly_staff,
                'staff_name' => $staffSetup->name,
                'staff_code' => $staffSetup->code,
                'staff_id' => $staffSetup->id,
            ];
        }

        return view('admin.attendenceSummary.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'monthly_attendance', 'month', 'year', 'monthYear'));
    }

    private function day_attendance($employ_id = '', $ind_date = null) {
        $attendences = ManualAttendanceList::where('tbl_manual_attendance_list.date', $ind_date)
                ->where('employee_id', $employ_id)
                ->first();


        $leaveDate = date('Y-m-d', strtotime($ind_date));
        $leaves = LeaveRequest::with('leaveType')
                ->where('employee_id', $employ_id)
                ->where('leave_from_a', '<=', $leaveDate)
                ->where('leave_to_a', '>=', $leaveDate)
                ->first();

        return [
            'attendence' => $attendences,
            'leave' => $leaves,
            'date' => $ind_date
        ];
    }

    public function print(Request $request) {
        $title = "Attendence Summarry";

        $monthYear = $request->month;

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);



        $monthly_attendance = [];
        $staffSetups = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        foreach ($staffSetups as $staffSetup) {

            $monthly_staff = [];
            for ($i = 1; $i <= $num_of_days; $i++) {
                $date = str_pad($i, 2, '0', STR_PAD_LEFT);
                $ind_date = $date . "-" . $month . "-" . $year;
                $monthly_staff[] = $this->day_attendance($staffSetup->id, $ind_date);
            }

            $monthly_attendance[] = [
                'monthly_staff' => $monthly_staff,
                'staff_id' => $staffSetup->id,
                'staff_name' => $staffSetup->name,
                'staff_code' => $staffSetup->code
            ];
        }

        $pdf = PDF::loadView('admin.attendenceSummary.print', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear]);

        return $pdf->stream('attendence_summary_' . $date . '.pdf');
    }

    public function inTime(Request $request) {
        $title = "In Time Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        return view('admin.attendenceSummary.inTime')->with(compact('title', 'monthly_attendance', 'month', 'year', 'monthYear', 'staff'));
    }

    public function inTimePrint(Request $request) {
        $title = "In Time Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        $pdf = PDF::loadView('admin.attendenceSummary.inTimePrint', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear, 'staff' => $staff]);

        return $pdf->stream('in_time_' . $monthYear . '.pdf');
    }

    public function lateTime(Request $request) {
        $title = "Late In Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        return view('admin.attendenceSummary.lateTime')->with(compact('title', 'monthly_attendance', 'month', 'year', 'monthYear', 'staff'));
    }

    public function lateTimePrint(Request $request) {
        $title = "Late In Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        $pdf = PDF::loadView('admin.attendenceSummary.lateTimePrint', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear, 'staff' => $staff]);

        return $pdf->stream('late_time_' . $monthYear . '.pdf');
    }

    public function leave(Request $request) {
        $title = "Leave Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff
        ];

        return view('admin.attendenceSummary.leave')->with(compact('title', 'monthly_attendance', 'month', 'year', 'monthYear', 'staff'));
    }

    public function leavePrint(Request $request) {
        $title = "Leave Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        $pdf = PDF::loadView('admin.attendenceSummary.leavePrint', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear, 'staff' => $staff]);

        return $pdf->stream('leave_' . $monthYear . '.pdf');
    }

    public function absent(Request $request) {
        $title = "Absent Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff
        ];

        return view('admin.attendenceSummary.absent')->with(compact('title', 'monthly_attendance', 'month', 'year', 'monthYear', 'staff'));
    }

    public function absentPrint(Request $request) {
        $title = "Absent Details";

        $monthYear = $request->month;

        $staff = StaffSetup::where('id', $request->staff)->first();

        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthly_staff = [];
        for ($i = 1; $i <= $num_of_days; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ind_date = $date . "-" . $month . "-" . $year;
            $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
        }

        $monthly_attendance[] = [
            'monthly_staff' => $monthly_staff,
        ];

        $pdf = PDF::loadView('admin.attendenceSummary.absentPrint', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear, 'staff' => $staff]);

        return $pdf->stream('absent_' . $monthYear . '.pdf');
    }

}
