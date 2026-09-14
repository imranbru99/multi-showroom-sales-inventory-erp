<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\StaffSetup;
use App\DealerSetup;
use App\SalesReturn;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ProductIssue;
use App\Services\StatementReport\Dealer;
use App\Services\StatementReport\Employee;

class RertailsSaleStateReportController extends Controller
{

    public function index(Request $request)
    {
        $title = "Retails Sales State Report";
        $searchFormLink = "retailsale.state.index";
        $printFormLink = "retailsale.state.print";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $data = [];

        if ($request->toDate == null) {
            $toDate = date('Y-m-d', strtotime(now()));
        }

        if ($print) {


            if ($request->criteria == "employee") {

                $employees = StaffSetup::get();

                $data = Employee::getRetailSalesReport($fromDate, $toDate);

            }
        }

        return view('admin.retailSaleStateReport.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'data'));
    }

    public function print(Request $request)
    {
        $title = "Dealer Sales State";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $staff = StaffSetup::find($request->staff);
        $totalSalesContributions = [];
        $salesContributions = [];
        $data = [];

        if ($request->toDate == null) {
            $toDate = date('Y-m-d', strtotime(now()));
        }

        if ($print) {

            $data = Employee::getRetailSalesReport($fromDate, $toDate);
        }

        $pdf = PDF::loadView('admin.retailSaleStateReport.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'data' => $data]);

        return $pdf->stream('retails_sales_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }
}
