<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShowroomSetup;
use App\SalaryProcessList;
use DB;
use PDF;

class PayrollPaymentHistoryController extends Controller {

    public function index(Request $request) {
        $title = "Payroll Payment History";
        $searchFormLink = "payrollPaymentHistory.index";
        $printFormLink = "payrollPaymentHistory.print";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $company = $request->company;
        $print = $request->print;

        $companies = ShowroomSetup::where('company_id', $this->company)->where('status', 1)->get();

        $payrollPayments = [];

        if ($company) {
            $payrollPayments = SalaryProcessList::with('staff', 'salaryProcess', 'staff')
                    ->whereBetween('payment_date', [$fromDate, $toDate])
                    ->where('payment_date', '>=', $fromDate)
                    ->where('payment_date', '<=', $toDate)
                    ->where('company_id', $this->company)
//                    ->where('showroom_id', $this->showroomId)
                    ->where('payment_status', 1)
                    ->get();
        }

        return view('admin.payrollPaymentHistory.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'companies', 'company', 'payrollPayments'));
    }

    public function print(Request $request) {
        $title = "Payroll Payment History";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $company = $request->company;
        $print = $request->print;

        $companies = ShowroomSetup::where('status', 1)->get();

        $payrollPayments = [];

        if ($company) {
            $payrollPayments = SalaryProcessList::with('staff', 'salaryProcess')
                    ->whereBetween('payment_date', [$fromDate, $toDate])
//                    ->where('payment_date', '>=' , $fromDate)
//                    ->where('payment_date', '<=' , $toDate)
                    ->where('company_id', $this->company)
//                    ->where('showroom_id', $this->showroomId)
                    ->where('payment_status', 1)
                    ->with('staff')
                    ->get();
        }


        $pdf = PDF::loadView('admin.payrollPaymentHistory.print', ['title' => $title, 'payrollPayments' => $payrollPayments, 'fromDate' => $fromDate, 'toDate' => $toDate]);

        return $pdf->stream('payroll_payment.pdf');
    }

}
