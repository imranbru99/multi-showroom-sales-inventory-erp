<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionIssue;
use App\ProductionIsuuList;
use DB;
use MPDF;
use PDF;

class ProductionHistoryController extends Controller {

    public function index(Request $request) {
        $title = "Production History";
        $searchFormLink = "productionHistory.index";
        $printFormLink = "productionHistory.print";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $productionHistories = ProductionIssue::with('staff', 'productName.product', 'producttionList')
                ->where('tbl_production_issue.showroom_id', $this->showroomId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->orderBy('tbl_production_issue.id', 'desc')
                ->get();

        return view('admin.productionHistory.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'productionHistories'));
    }

    public function print(Request $request) {
        $title = "Production History";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $productionHistories = ProductionIssue::with('staff', 'productName.product', 'producttionList')
                ->where('tbl_production_issue.showroom_id', $this->showroomId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->orderBy('tbl_production_issue.id', 'desc')
                ->get();

        $pdf = PDF::loadView('admin.productionHistory.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'productionHistories' => $productionHistories]);

        return $pdf->stream('production_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
