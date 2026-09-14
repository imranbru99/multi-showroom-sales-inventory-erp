<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\StaffSetup;
use App\InstallmentSchedule;
use PDF;

class UnpaidCustomerController extends Controller {

    public function index(Request $request) {
        $title = "Unpaid Customers";
        $searchFormLink = "unpaidcustomer.index";
        $printFormLink = "unpaidcustomer.print";

        $print = $request->print;

        $days = $request->days;
        $sales_by = $request->sales_by;
        $fromDate = date('Y-m-d', strtotime(now()->subDays($days)));
        $toDate = date('Y-m-d');

        $staffs = StaffSetup::where('status', 1)->get();


        $scheduleCustomerIds = InstallmentSchedule::with('installment')
                ->whereBetween('installment_schedule_date', [$fromDate, $toDate])
                ->orderBy('installment_schedule_date', 'asc')
                ->where('status', 1)
                ->get()
                ->pluck('installment.customer_id')
                ->toArray();

        $customers = CustomerRegistrationSetup::whereIn('id', $scheduleCustomerIds)
                ->whereHas('agreement', function ($q) use ($sales_by) {
                    if ($sales_by) {
                        $q->where('employee_id', $sales_by);
                    }
                })
                ->get();

        return view('admin.unpaidCustomer.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'days', 'customers', 'staffs', 'sales_by'));
    }

    public function print(Request $request) {
        $title = "Unpaid Customers";

        $days = $request->days;
        $fromDate = date('Y-m-d', strtotime(now()->subDays($days)));
        $toDate = date('Y-m-d');
        $sales_by = $request->sales_by;
        $staff = StaffSetup::find($sales_by);

        $scheduleCustomerIds = InstallmentSchedule::with('installment')
                ->whereBetween('installment_schedule_date', [$fromDate, $toDate])
                ->orderBy('installment_schedule_date', 'asc')
                ->where('status', 1)
                ->get()
                ->pluck('installment.customer_id')
                ->toArray();

        $customers = CustomerRegistrationSetup::whereIn('id', $scheduleCustomerIds)
                ->whereHas('agreement', function ($q) use ($sales_by) {
                    if ($sales_by) {
                        $q->where('employee_id', $sales_by);
                    }
                })
                ->get();

        $pdf = PDF::loadView('admin.unpaidCustomer.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'customers' => $customers, 'sales_by' => $sales_by, 'staff' => $staff]);

        return $pdf->stream('unpaid_customers_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
