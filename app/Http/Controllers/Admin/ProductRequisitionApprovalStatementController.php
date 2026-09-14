<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use DB;
use MPDF;
use PDF;

class ProductRequisitionApprovalStatementController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Requisition And Approval Statement";
        $searchFormLink = "productRequisitionApprovalStatement.index";
        $printFormLink = "productRequisitionApprovalStatement.print";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $requisitionApprovalStatements = array();

        $requisitionApprovalStatements = DB::table('view_product_requisition_approval_statement')
                ->select('date as date', 'dealerId as dealerId', 'dealerName as dealerName', 'productName as productName', 'productModelNo as productModelNo', 'requisitionQty as requisitionQty', 'approvedQty as approvedQty')
                ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($dealer) {
                        $query->whereIn('dealerId', $dealer);
                    }
                })
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
                ->orderBy('dealerName', 'asc')
                ->orderBy('productName', 'asc')
                ->get();

        return view('admin.productRequisitionApprovalStatement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'dealers', 'dealer', 'fromDate', 'toDate', 'requisitionApprovalStatements'));
    }

    public function print(Request $request) {
        $title = "Print Requisition And Approval Statement";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $requisitionApprovalStatements = array();

        $requisitionApprovalStatements = DB::table('view_product_requisition_approval_statement')
                ->select('date as date', 'dealerId as dealerId', 'dealerName as dealerName', 'productName as productName', 'productModelNo as productModelNo', 'requisitionQty as requisitionQty', 'approvedQty as approvedQty')
                ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($dealer) {
                        $query->whereIn('dealerId', $dealer);
                    }
                })
                ->where('companyId', $this->company)
                ->where('showroomId', $this->showroomId)
                ->orderBy('dealerName', 'asc')
                ->orderBy('productName', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.productRequisitionApprovalStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'requisitionApprovalStatements' => $requisitionApprovalStatements]);

        return $pdf->stream('product_requisition_approval_statement_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
