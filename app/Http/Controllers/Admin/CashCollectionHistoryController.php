<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\Product;
use App\RetailSale;
use App\StaffSetup;
use App\CashCollection;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\InstallmentCollection;
use App\InstallmentCollectionList;
use App\Http\Controllers\Controller;

class CashCollectionHistoryController extends Controller {

    public function index(Request $request) {
        $title = "Cash Collection History";
        $searchFormLink = "cashCollectionHistory.index";
        $printFormLink = "cashCollectionHistory.print";
        $btnVal = $request->btnVal;

        $cashCollectionHistoryList = [];

        $collectorParameter = $request->collector;
        $print = $request->print;

        $invoice_no = $request->invoice_no;

        // dd($collectorParameter);

        $collectorList = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $fromDate = date('Y-m-d', strtotime(now()));
        if ($request->fromDate) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        $toDate = date('Y-m-d', strtotime(now()));
        if ($request->toDate) {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }


        if ($request->btnVal == "History") {
            $cashCollectionHistoryList = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
                    ->where('installment_schedule_amount', '!=', 0)
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('installment_collection_date', '>=', $fromDate)
                    ->where('installment_collection_date', '<=', $toDate);

            if ($invoice_no) {
                $cashCollectionHistoryList = $cashCollectionHistoryList->where('invoice_no', $invoice_no);
            }

            if ($collectorParameter) {
                $cashCollectionHistoryList = $cashCollectionHistoryList->whereHas('collection.collector', function ($q) use ($collectorParameter) {
                    $q->whereIn('reference_id', $collectorParameter);
                });
            }

            $cashCollectionHistoryList = $cashCollectionHistoryList->get();
        }

        if ($request->btnVal == "Summary") {

            $cashCollectionHistoryList = InstallmentCollectionList::with(['collection.customer', 'collection.collector'])
                    ->where('installment_schedule_amount', '!=', 0)
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('installment_collection_date', '>=', $fromDate)
                    ->where('installment_collection_date', '<=', $toDate);

            if ($collectorParameter) {
                $cashCollectionHistoryList = $cashCollectionHistoryList->whereHas('collection.collector', function ($q) use ($collectorParameter) {
                    $q->whereIn('reference_id', $collectorParameter);
                });
            }
            $cashCollectionHistoryList = $cashCollectionHistoryList->get();

            $cashCollectionHistoryList = $cashCollectionHistoryList->groupBy('collection.customer.id');
            // dd($cashCollectionHistoryList);
        }


        return view('admin.cashCollectionHistory.index')->with(compact('btnVal', 'fromDate', 'toDate', 'title', 'searchFormLink', 'printFormLink', 'print', 'collectorList', 'cashCollectionHistoryList', 'collectorParameter', 'invoice_no'));
    }

    public function dateWiseCollection(Request $request) {

        // dd($request);

        if ($request->start_date) {

            $start_date = $request->start_date;
        } else {
            $start_date = null;
        }

        if ($request->end_date) {

            $end_date = $request->end_date;
        } else {
            $end_date = null;
        }


        $dates = new CarbonPeriod(date('Y-m-d', strtotime($start_date)), date('Y-m-d', strtotime($end_date)));
        $dates = $dates->toArray();

        $data = [];

        foreach ($dates as $date) {

            $collection = InstallmentCollectionList::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('installment_collection_date', $date->format('Y-m-d'))
                    ->sum('installment_schedule_amount');

            if ($collection == 0) {
                continue;
            }

            $data[] = [
                'date' => $date->format('d-m-Y'),
                'collection' => $collection,
            ];
        }

        return view('admin.tempCollectionReport')->with(compact('start_date', 'end_date', 'data'));
    }

    public function dateWiseSales(Request $request) {
        // dd($request);

        if ($request->start_date) {

            $start_date = $request->start_date;
        } else {
            $start_date = null;
        }

        if ($request->end_date) {

            $end_date = $request->end_date;
        } else {
            $end_date = null;
        }


        $dates = new CarbonPeriod(date('Y-m-d', strtotime($start_date)), date('Y-m-d', strtotime($end_date)));
        $dates = $dates->toArray();

        $data = [];

        foreach ($dates as $date) {

            $sale = RetailSale::where('sale_date', $date->format('Y-m-d'))
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->sum('deposite');

            if ($sale == 0) {
                continue;
            }

            $data[] = [
                'date' => $date->format('d-m-Y'),
                'sale' => $sale,
            ];
        }

        return view('admin.tempSaleReport')->with(compact('start_date', 'end_date', 'data'));
    }

    public function print(Request $request) {

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $btnVal = $request->btnVal;
        $invoice_no = $request->invoice_no;
        $cashCollectionHistoryList = [];

        $collectorParameter = $request->collector;
        $staffs = array();

        // $staffs = StaffSetup::whereIn('id', $collectorParameter)->select(['name'])->get();

        if ($request->fromDate) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        } else {
            $fromDate = date('Y-m-d', strtotime(now()));
        }

        if ($request->toDate) {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        } else {
            $toDate = date('Y-m-d', strtotime(now()));
        }

        if ($request->btnVal == "History") {

            $title = "Collection History from " . date('d-m-Y', strtotime($request->fromDate)) . " to " . date('d-m-Y', strtotime($request->toDate));

            $cashCollectionHistoryList = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector', 'showroom'])
                    ->where('installment_schedule_amount', '!=', 0)
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('installment_collection_date', '>=', $fromDate)
                    ->where('installment_collection_date', '<=', $toDate);

            if ($invoice_no) {
                $cashCollectionHistoryList = $cashCollectionHistoryList->where('invoice_no', $invoice_no);
            }

            if ($collectorParameter) {
                $staffs = StaffSetup::whereIn('id', $collectorParameter)->select(['name'])->get();
                $cashCollectionHistoryList = $cashCollectionHistoryList->whereHas('collection.collector', function ($q) use ($collectorParameter) {
                    $q->whereIn('reference_id', $collectorParameter);
                });
            }

            $cashCollectionHistoryList = $cashCollectionHistoryList->get();
        }

        if ($request->btnVal == "Summary") {

            $title = "Collection Summary from " . date('d-m-Y', strtotime($request->fromDate)) . " to " . date('d-m-Y', strtotime($request->toDate));

            $cashCollectionHistoryList = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
                    ->where('installment_schedule_amount', '!=', 0)
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('installment_collection_date', '>=', $fromDate)
                    ->where('installment_collection_date', '<=', $toDate);

            if ($collectorParameter) {
                $staffs = StaffSetup::whereIn('id', $collectorParameter)->select(['name'])->get();
                $cashCollectionHistoryList = $cashCollectionHistoryList->whereHas('collection.collector', function ($q) use ($collectorParameter) {
                    $q->whereIn('reference_id', $collectorParameter);
                });
            }

            $cashCollectionHistoryList = $cashCollectionHistoryList->get();

            $cashCollectionHistoryList = $cashCollectionHistoryList->groupBy('collection.customer.id');
        }

        $pdf = PDF::loadView('admin.cashCollectionHistory.print', ['btnVal' => $btnVal, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'cashCollectionHistoryList' => $cashCollectionHistoryList, 'staffs' => $staffs, 'invoice_no' => $invoice_no]);

        return $pdf->stream('cash_collection_list');
    }

}
