<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use DB;
use MPDF;
use PDF;

class ProductionRequisitionStatementApprovalController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Requisition And Approval Statement";
        $searchFormLink = "productionRequisitionApprovalStatement.index";
        $printFormLink = "productionRequisitionApprovalStatement.print";

        $staff = $request->staff;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $requisitionApprovalStatements = array();

        $requisitionApprovalStatements = DB::table('view_production_requisition_statement')
                ->select('date as date', 'staffId as staffId', 'staffName as staffName', 'productName as productName', 'productModelNo as productModelNo', 'requisitionQty as requisitionQty', 'approvedQty as approvedQty')
                ->orWhere(function($query) use($fromDate, $toDate, $staff) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($staff) {
                        $query->whereIn('staffId', $staff);
                    }
                })
                ->where('showroomId', $this->showroomId)
                ->orderBy('staffName', 'asc')
                ->orderBy('productName', 'asc')
                ->get();

        return view('admin.productionRequisitionApprovalStatement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'staffs', 'staff', 'fromDate', 'toDate', 'requisitionApprovalStatements'));
    }

    public function print(Request $request) {
        $title = "Print Requisition And Approval Statement";

        $staff = $request->staff;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $requisitionApprovalStatements = array();

        $requisitionApprovalStatements = DB::table('view_production_requisition_statement')
                ->select('date as date', 'staffId as staffId', 'staffName as staffName', 'productName as productName', 'productModelNo as productModelNo', 'requisitionQty as requisitionQty', 'approvedQty as approvedQty')
                ->orWhere(function($query) use($fromDate, $toDate, $staff) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($staff) {
                        $query->whereIn('staffId', $staff);
                    }
                })
                ->where('showroomId', $this->showroomId)
                ->orderBy('staffName', 'asc')
                ->orderBy('productName', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.productionRequisitionApprovalStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'requisitionApprovalStatements' => $requisitionApprovalStatements]);

        return $pdf->stream('production_requisition_approval_statement_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
