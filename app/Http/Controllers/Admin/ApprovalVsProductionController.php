<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionRequisition;
use App\ProductionRequisitonInfo;
use App\Product;
use App\StaffSetup;
use DB;
use MPDF;
use PDF;

class ApprovalVsProductionController extends Controller {

    public function index(Request $request) {
        $title = "Approval Vs Production";
        $searchFormLink = "approvalVsProduction.index";
        $printFormLink = "approvalVsProduction.print";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $approvalVsProduction = ProductionRequisitonInfo::select('tbl_production_requisition_info.*', 'tbl_production_requisitions.requisition_no', 'tbl_production_requisitions.date')
                ->with('requisition.staff')
                ->leftJoin('tbl_production_requisitions', 'tbl_production_requisitions.id', '=', 'tbl_production_requisition_info.requisition_id')
                ->where('tbl_production_requisition_info.showroom_id', $this->showroomId)
                ->whereBetween('tbl_production_requisitions.date', [$fromDate, $toDate])
                ->whereNotNull('tbl_production_requisition_info.approved_qty')
                ->orderBy('tbl_production_requisition_info.id', 'desc')
                ->get();

//        dd($approvalVsProduction);

        return view('admin.approvalVsProduction.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'approvalVsProduction'));
    }

    public function print(Request $request) {
        $title = "Approval Vs Production";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $approvalVsProduction = ProductionRequisitonInfo::select('tbl_production_requisition_info.*', 'tbl_production_requisitions.requisition_no', 'tbl_production_requisitions.date')
                ->with('requisition.staff')
                ->leftJoin('tbl_production_requisitions', 'tbl_production_requisitions.id', '=', 'tbl_production_requisition_info.requisition_id')
                ->where('tbl_production_requisition_info.showroom_id', $this->showroomId)
                ->whereBetween('tbl_production_requisitions.date', [$fromDate, $toDate])
                ->whereNotNull('tbl_production_requisition_info.approved_qty')
                ->orderBy('tbl_production_requisition_info.id', 'desc')
                ->get();

        $pdf = PDF::loadView('admin.approvalVsProduction.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'approvalVsProduction' => $approvalVsProduction]);

        return $pdf->stream('approval_vs_production_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
