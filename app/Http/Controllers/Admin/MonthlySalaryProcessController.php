<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalaryProcess;
use App\SalaryProcessList;
use App\StaffSetup;
use App\EmployeeAllowance;
use App\ManualAttendance;
use App\ManualAttendanceList;
use App\LeaveRequest;
use App\SalarySetup;
use App\ShowroomSetup;
use DB;
use PDF;
use MPDF;

class MonthlySalaryProcessController extends Controller {

    public function index(Request $request) {
        $title = "Monthly Salary Process";

        $month = $request->month;
        $year = $request->year;
        $company = $this->company;

        $month_year = date('Y-m', strtotime($year . '-' . $month));

        $companies = ShowroomSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('id', $this->showroomId)
                ->get();

        DB::beginTransaction();
        try {
            if ($month != '' && $year != '') {
                $existMonthYear = SalaryProcess::where('company_id', $company)
                        ->where('month_year', $month_year)
                        ->first();
                if (!empty($existMonthYear)) {
                    $monthlySalaryProcess = SalaryProcess::with('salaryProcessList', 'showroom')
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->orderBy('id', 'desc')
                            ->get();
                    session()->flash('msg', 'Month Already Exist!');
                    return view('admin.monthlySalaryProcess.index')->with('msg', 'Month Already Exist!')->with(compact('title', 'monthlySalaryProcess', 'month', 'year', 'companies', 'company'));
                } else {
                    $process = SalaryProcess::create([
                                'showroom_id' => $this->showroomId,
                                'month_year' => $month_year,
                                'company_name' => '',
                                'company_id' => $this->company,
                                'created_by' => $this->userId
                    ]);

                    $staffs = StaffSetup::where('status', 1)->get();

                    if ($month != '' && $year != '') {
                        foreach ($staffs as $staff) {

                            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                            $monthly_staff = [];
                            for ($i = 1; $i <= $num_of_days; $i++) {
                                $date = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $ind_date = $date . "-" . $month . "-" . $year;
                                $monthly_staff[] = $this->day_attendance($staff->id, $ind_date);
                            }

                            $leaves = 0;
                            $attends_in_time = 0;
                            $attends_late_time = 0;
                            $absents = 0;
                            $holidays = 0;
                            foreach ($monthly_staff as $key => $attendence) {

                                if (date('l', strtotime($attendence['date'])) != 'Friday') {
                                    if ($attendence['attendence'] == '') {
                                        if ($attendence['leave'] != '') {
                                            $leaves += 1;
                                        } else {
                                            $absents += 1;
                                        }
                                    } elseif ($attendence['attendence'] != '') {
                                        if ($attendence['attendence']->in_time <= "09:30:00") {
                                            $attends_in_time += 1;
                                        } elseif ($attendence['attendence']->in_time > "09:30:00") {
                                            $attends_late_time += 1;
                                        }
                                    }
                                } else {
                                    $holidays += 1;
                                }
                            }


                            if ($absents + $holidays == $num_of_days) {
                                continue;
                            }


                            $allowance = EmployeeAllowance::with('employeeAllowanceList')->where('staff_id', $staff->id)->first();

                            $basic = 0;
                            $house_rent = 0;
                            $medical = 0;
                            $conveyance = 0;
                            $children = 0;
                            $ait = 0;
                            if (!empty($allowance)) {
                                $late_charge = 0;
                                $abcent_charge = 0;
                                foreach ($allowance->employeeAllowanceList as $allowance_details) {
                                    if ($allowance_details->allowance_name == 'Basic') {
                                        $basic = $allowance_details->allowance_amount;
                                    }
                                    if ($allowance_details->allowance_name == 'House Rent') {
                                        $house_rent = $allowance_details->allowance_amount;
                                    }
                                    if ($allowance_details->allowance_name == 'Medical') {
                                        $medical = $allowance_details->allowance_amount;
                                    }
                                    if ($allowance_details->allowance_name == 'Conveyance') {
                                        $conveyance = $allowance_details->allowance_amount;
                                    }
                                    if ($allowance_details->allowance_name == 'Children') {
                                        $children = $allowance_details->allowance_amount;
                                    }
                                    if ($allowance_details->allowance_name == 'AIT') {
                                        $ait = $allowance_details->allowance_amount;
                                    }
                                }


                                $salarySetups = SalarySetup::where('status', 1)->get();

                                $daily_salary = intval($allowance->total_amount / 30);

                                foreach ($salarySetups as $salarySetup) {
                                    if ($salarySetup->id == 1) {
                                        $lateDays = intval($attends_late_time / $salarySetup->cut_on);

                                        $late_charge = 0;
                                        if ($lateDays > 0) {
                                            $late_charge = $daily_salary * ($lateDays * $salarySetup->salary_cut);
                                        }
                                    }

                                    if ($salarySetup->id == 2) {
                                        $absentDays = intval($absents / $salarySetup->cut_on);

                                        $abcent_charge = 0;
                                        if ($absentDays > 0) {
                                            $abcent_charge = $daily_salary * ($absentDays * $salarySetup->salary_cut);
                                        }
                                    }
                                }

                                $totalSalary = (($basic + $house_rent + $medical + $conveyance + $children) - ($ait + $late_charge + $abcent_charge));

                                SalaryProcessList::create([
                                    'company_id' => $this->company,
                                    'showroom_id' => $this->showroomId,
                                    'salary_process_id' => $process->id,
                                    'employee_id' => $staff->id,
                                    'month_year' => $month_year,
                                    'salary_amount' => $allowance->total_amount,
                                    'basic' => $basic,
                                    'house_rent' => $house_rent,
                                    'medical' => $medical,
                                    'conveyance' => $conveyance,
                                    'children' => $children,
                                    'ait' => $ait,
                                    'commission_payable' => 0,
                                    'late_charge' => $late_charge,
                                    'abcent_charge' => $abcent_charge,
                                    'total_payable' => $totalSalary,
                                    'created_by' => $this->userId
                                ]);
                            }
                        }
                    }

                    DB::commit();
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
        }

        $monthlySalaryProcess = SalaryProcess::with('salaryProcessList', 'showroom')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.monthlySalaryProcess.index')->with(compact('title', 'monthlySalaryProcess', 'month', 'year', 'companies', 'company'));
    }

    public function delete(Request $request) {
        SalaryProcessList::where('salary_process_id', $request->salaryProcess)->delete();
        SalaryProcess::where('id', $request->salaryProcess)->delete();
    }

    ///dfnsdnfjsdjsdjbgsjbdgjsbdg////
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

    public function view($id) {
        $title = "Monthly Salary Sheet";

        $monthlySalary = SalaryProcess::where('id', $id)->with('salaryProcessList', 'salaryProcessList.staff')->first();

        $printLink = 'monthlySalaryProcess.print';
        $printId = $monthlySalary->id;

        return view('admin.monthlySalaryProcess.view')->with(compact('title', 'monthlySalary', 'printLink', 'printId'));
    }

    public function print($id) {
        $title = "Monthly Salary Sheet";

        $monthlySalary = SalaryProcess::where('id', $id)->with('salaryProcessList', 'salaryProcessList.staff')->first();

        $pdf = PDF::loadView('admin.monthlySalaryProcess.print', ['monthlySalary' => $monthlySalary, 'title' => $title], [], ['format' => 'A4-L']);

        return $pdf->stream('monthly_salary_sheet_' . $monthlySalary->month_year . '.pdf');
    }

    public function viewByEmployee($id) {
        $title = "Salary Slip";

        $monthlySalary = SalaryProcessList::where('id', $id)->with('staff')->first();

        $staticLink = 'monthlySalaryProcess.view';
        $viewId = $monthlySalary->salary_process_id;

        return view('admin.monthlySalaryProcess.paySlip')->with(compact('title', 'monthlySalary', 'staticLink', 'viewId'));
    }

    public function payment(Request $request) {
        $title = "Salary Slip";

        $request->paymentId;
        $monthlySalary = SalaryProcessList::where('id', $request->paymentId)->with('staff')->first();

        $monthlySalary->update([
            'payment_date' => date('Y-m-d'),
            'payment_status' => 1,
        ]);

        return redirect(route('monthlySalaryProcess.printPaySlip', $monthlySalary->id));
    }

    public function printPaySlip($id) {
        $title = "Salary Slip";

        $monthlySalary = SalaryProcessList::where('id', $id)->with('staff')->first();

        $pdf = PDF::loadView('admin.monthlySalaryProcess.printPaySlip', ['monthlySalary' => $monthlySalary, 'title' => $title]);

        return $pdf->stream('salary_slip_' . $monthlySalary->month_year . '.pdf');
    }

}
