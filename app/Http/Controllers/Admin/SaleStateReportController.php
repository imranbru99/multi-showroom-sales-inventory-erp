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

class SaleStateReportController extends Controller {

    public function index(Request $request) {
        $title = "Sales State Report";
        $searchFormLink = "sale.state.index";
        $printFormLink = "sale.state.print";
        $criteria = $request->criteria;
        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->get();

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate2 = date('Y-m-d', strtotime($request->toDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $staffId = $request->staff;
        $totalSalesContributions = [];
        $salesContributions = [];
        $data = [];

        if ($request->toDate == null) {
            $toDate = date('Y-m-d', strtotime(now()));
        }

        if ($print) {

            $criteria = $request->criteria;

            $dealerCriterias = ['dealer', 'd_internal', 'd_external', 'employee_dealer'];

            // dd($request);

            if (in_array($criteria, $dealerCriterias)) {

                switch ($criteria) {
                    case 'd_internal':
                        $dealers = DealerSetup::where('type', 'Internal')
                                ->where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;

                    case 'd_external':
                        $dealers = DealerSetup::where('type', 'External')
                                ->where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;

                    case 'employee_dealer':

                        $dealerIds = ProductIssue::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->where('sales_by', $request->staff)
                                ->select('dealer_id')
                                ->groupBy('dealer_id')
                                ->get()
                                ->pluck('dealer_id')
                                ->toArray();

                        $dealers = DealerSetup::whereIn('id', $dealerIds)->get();

                        break;

                    default:
                        $dealers = DealerSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;
                }

                $data = Dealer::getSaleState($dealers, $fromDate, $toDate);
            }


            if ($request->criteria == "employee") {

                $employees = StaffSetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $data = Employee::getSaleState($employees, $fromDate, $toDate);
            }

            if ($request->criteria == "dealer_type") {

                $types = ['Internal', 'External'];

                $data = [];

                foreach ($types as $type) {

                    $sales = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->where('date', '>=', $fromDate)
                                ->where('date', '<=', $toDate);
                            })
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalSales = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('amount');


                    $totalReturn = SalesReturn::with(['dealer'])
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('amount');


                    $dcollection = DealerCollection::with('dealer')
                            ->where('remarks', '!=', 'adjust')
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->whereBetween('payment_date', [$fromDate, $toDate])
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('payment_amount');

                    $advanceCollection = AdvanceCollection::with('dealer')
                            ->where('advance_amount', '>', 0)
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->whereBetween('date', [$fromDate, $toDate])
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('advance_amount');

                    $collection = $dcollection + $advanceCollection;

                    $issueReturn = SalesReturn::with(['dealer'])
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('return_date', '>=', $fromDate)
                            ->where('return_date', '<=', $toDate)
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                        continue;
                    }


                    $grandTotal = $totalSales - $totalReturn;
                    $totalCollection = $collection;

                    $outStanding = $grandTotal - $totalCollection;

                    $ld = [
                        'employeeName' => $type,
                        'purchase' => $sales->sum('amount'),
                        'collection' => $collection,
                        'return' => $issueReturn->sum('amount'),
                        'returnQty' => $issueReturn->sum('qty'),
                        'balance' => $sales->sum('amount') - ($collection) - $issueReturn->sum('amount'),
                        'qty' => $sales->sum('qty'),
                        'outstanding' => $outStanding,
                    ];

                    array_push($data, $ld);
                }
            }
        }

        return view('admin.saleStateReport.index')->with(compact('staffId', 'staffs', 'title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'toDate2', 'salesContributions', 'criteria', 'totalSalesContributions', 'data'));
    }

    public function print(Request $request) {
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

            $criteria = $request->criteria;

            $dealerCriterias = ['dealer', 'd_internal', 'd_external', 'employee_dealer'];


            if (in_array($criteria, $dealerCriterias)) {

                switch ($criteria) {
                    case 'd_internal':
                        $dealers = DealerSetup::where('type', 'Internal')
                                ->where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;

                    case 'd_external':
                        $dealers = DealerSetup::where('type', 'External')
                                ->where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;

                    case 'employee_dealer':

                        $dealerIds = ProductIssue::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->where('sales_by', $request->staff)
                                ->select('dealer_id')
                                ->groupBy('dealer_id')
                                ->get()
                                ->pluck('dealer_id')
                                ->toArray();

                        $dealers = DealerSetup::whereIn('id', $dealerIds)->get();

                        break;

                    default:
                        $dealers = DealerSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->get();
                        break;
                }

                $data = Dealer::getSaleState($dealers, $fromDate, $toDate);
            }

            if ($request->criteria == "employee") {

                $title = "Employee Sales State";

                $employees = StaffSetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $data = Employee::getSaleState($employees, $fromDate, $toDate);
            }

            if ($request->criteria == "dealer_type") {

                $types = ['Internal', 'External'];

                $data = [];

                foreach ($types as $type) {

                    $sales = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $totalSales = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('amount');


                    $totalReturn = SalesReturn::with(['dealer'])
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('amount');

                    $dcollection = DealerCollection::with('dealer')
                            ->where('remarks', '!=', 'adjust')
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->whereBetween('payment_date', [$fromDate, $toDate])
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('payment_amount');

                    $advanceCollection = AdvanceCollection::with('dealer')
                            ->where('advance_amount', '>', 0)
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->whereBetween('date', [$fromDate, $toDate])
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->sum('advance_amount');

                    $collection = $dcollection + $advanceCollection;

                    $issueReturn = SalesReturn::with(['dealer'])
                            ->whereHas('dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('return_date', '>=', $fromDate)
                            ->where('return_date', '<=', $toDate)
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                        continue;
                    }

                    $grandTotal = $totalSales - $totalReturn;
                    $totalCollection = $collection;

                    $outStanding = $grandTotal - $totalCollection;

                    $ld = [
                        'employeeName' => $type,
                        'purchase' => $sales->sum('amount'),
                        'collection' => $collection,
                        'return' => $issueReturn->sum('amount'),
                        'returnQty' => $issueReturn->sum('qty'),
                        'balance' => $sales->sum('amount') - ($collection) - $issueReturn->sum('amount'),
                        'qty' => $sales->sum('qty'),
                        'outstanding' => $outStanding,
                    ];

                    array_push($data, $ld);
                }
            }
        }

        $pdf = PDF::loadView('admin.saleStateReport.print', ['staff' => $staff, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'salesContributions' => $salesContributions, 'criteria' => $criteria, 'totalSalesContributions' => $totalSalesContributions, 'data' => $data]);

        return $pdf->stream('dealer_sales_state_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
