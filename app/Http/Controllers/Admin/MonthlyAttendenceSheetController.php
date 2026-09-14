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

class MonthlyAttendenceSheetController extends Controller {

    public function index(Request $request) {
        $title = "Monthly Attendence Sheet";
        $searchFormLink = 'monthlyAttendenceSheet.index';
        $printFormLink = 'monthlyAttendenceSheet.print';

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
                'staff_code' => $staffSetup->code
            ];
        }
//        dd($monthly_attendance);

        return view('admin.monthlyAttendenceSheet.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'monthly_attendance', 'month', 'year', 'monthYear'));
    }

    private function day_for($num_of_days) {
        $monthly_staf = [];
        for ($i = 1; $i <= $num_of_days; $i++) {

            $monthly_staf[] = [$i];
        }
        $monthly_staf[] = [$i];
    }

    private function day_attendance($employ_id = '', $ind_date = null) {
        $attendences = ManualAttendanceList::with('staff')
                ->where('tbl_manual_attendance_list.date', $ind_date)
                ->where('employee_id', $employ_id)
                ->first();


        $leaveDate = date('Y-m-d', strtotime($ind_date));
        $leaves = LeaveRequest::with('staff')
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
        $title = "Monthly Attendence Sheet";

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
                'staff_code' => $staffSetup->code
            ];
        }

        $pdf = PDF::loadView('admin.monthlyAttendenceSheet.print', ['monthly_attendance' => $monthly_attendance, 'title' => $title, 'month' => $month, 'year' => $year, 'monthYear' => $monthYear]);

        return $pdf->stream('monthly_attendence_sheet_' . $date . '.pdf');
    }

}
