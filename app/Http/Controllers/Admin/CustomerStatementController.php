<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;

use MPDF;

use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use App\Services\Statement\Customer\Statement;

class CustomerStatementController extends Controller
{
    public function index(Request $request)
    {
        $title = "Customer Statement";
        $searchFormLink = "customerStatement.index";
        $printFormLink = "customerStatement.print";

        $customer = $request->customer;
        $customerId = $request->customer;

        $fromDate = date('Y-m-d', strtotime('01-09-2019'));
        // $fromDate = date('Y-m-d', strtotime(now()));
        $toDate = date('Y-m-d', strtotime(now()));

        if ($request->fromDate) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate) {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $print = $request->print;


        $customers = CustomerRegistrationSetup::where('status', 1)->get();


        $previousBalance = [];
        $customerStatements = [];

        if ($print) {
            $previousBalance = Statement::getPreviousBalance($customer, $lastDate);

            $customerStatements = Statement::getCustomerStatement($customer, $fromDate, $toDate, $previousBalance);
        }

        return view('admin.customerStatement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'customers', 'customer', 'fromDate', 'toDate', 'previousBalance', 'customerStatements', 'customerId'));
    }

    public function print(Request $request)
    {
        $title = "Print Customer Statement";
        $customer = $request->customer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $previousBalances = array();
        $customerStatements = array();

        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));

        $previousBalance = [];
        $customerStatements = [];

        $previousBalance = Statement::getPreviousBalance($customer, $lastDate);

        $customerStatements = Statement::getCustomerStatement($customer, $fromDate, $toDate, $previousBalance);

        $pdf = PDF::loadView('admin.customerStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'previousBalance' => $previousBalance, 'customerStatements' => $customerStatements]);

        return $pdf->stream('customer_statement_' . $fromDate . '_to_' . $toDate . '.pdf');
    }
}
