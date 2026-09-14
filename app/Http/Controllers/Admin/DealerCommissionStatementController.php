<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use DB;
use MPDF;
use PDF;

class DealerCommissionStatementController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Dealer Commission Report";
        $searchFormLink = "dealerCommissionStatement.index";
        $printFormLink = "dealerCommissionStatement.print";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $btnSummary = $request->btnSummary;
        $btnRecord = $request->btnRecord;

        $dealers = DealerSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        if ($request->btnSummary == "Summary") {
            $dealerCommissionSummaries = DB::table('view_dealer_commission_statement')
                    ->select('date as date', 'dealerId as dealerId', 'dealerName as dealerName', DB::raw('SUM(commissionAmount) as totalCommissionAmount'))
                    ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('date', array($fromDate, $toDate));
                        }

                        if ($dealer) {
                            $query->whereIn('dealerId', $dealer);
                        }
                    })
                    ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                    ->groupBy('dealerId')
                    ->get();
        } else {
            $dealerCommissionSummaries = "";
        }

        if ($request->btnRecord == "Record") {
            $dealerCommissionStatements = DB::table('view_dealer_commission_statement')
                    ->select('date as date', 'dealerId as dealerId', 'dealerName as dealerName', 'categoryName as categoryName', 'saleAmount as saleAmount', 'commissionRate as commissionRate', 'commissionAmount as commissionAmount')
                    ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('date', array($fromDate, $toDate));
                        }

                        if ($dealer) {
                            $query->whereIn('dealerId', $dealer);
                        }
                    })
                    ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                    ->get();
        } else {
            $dealerCommissionStatements = "";
        }

        return view('admin.dealerCommissionStatement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'btnSummary', 'btnRecord', 'dealers', 'dealer', 'fromDate', 'toDate', 'dealerCommissionStatements', 'dealerCommissionSummaries'));
    }

    public function print(Request $request) {

        // $title = "Print Dealers Commission Report";

        $title = $request->btnPrintSummary ? "Print Dealers Commission Report Summary" : "Print Dealers Commission Report Details";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $btnPrintSummary = $request->btnPrintSummary;
        $btnPrintRecord = $request->btnPrintRecord;

        if ($request->btnPrintSummary == "Print Summary") {
            $dealerCommissionSummaries = DB::table('view_dealer_commission_statement')
                    ->select('view_dealer_commission_statement.date as date', 'view_dealer_commission_statement.dealerId as dealerId', 'view_dealer_commission_statement.dealerName as dealerName', 'tbl_dealers.mobile as dealerPhone', 'tbl_dealers.address as dealerAddress', DB::raw('SUM(view_dealer_commission_statement.commissionAmount) as totalCommissionAmount'))
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'view_dealer_commission_statement.dealerId')
                    ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('view_dealer_commission_statement.date', array($fromDate, $toDate));
                        }

                        if ($dealer) {
                            $query->whereIn('view_dealer_commission_statement.dealerId', $dealer);
                        }
                    })
                    ->where('view_dealer_commission_statement.companyId', $this->company)
//                    ->where('view_dealer_commission_statement.showroomId', $this->showroomId)
                    ->groupBy('view_dealer_commission_statement.dealerId')
                    ->get();
        } else {
            $dealerCommissionSummaries = "";
        }

        if ($request->btnPrintRecord == "Print Record") {
            $dealerCommissionStatements = DB::table('view_dealer_commission_statement')
                    ->select('view_dealer_commission_statement.date as date', 'view_dealer_commission_statement.dealerId as dealerId', 'view_dealer_commission_statement.dealerName as dealerName', 'tbl_dealers.mobile as dealerPhone', 'tbl_dealers.address as dealerAddress', 'view_dealer_commission_statement.categoryName as categoryName', 'view_dealer_commission_statement.saleAmount as saleAmount', 'view_dealer_commission_statement.commissionRate as commissionRate', 'view_dealer_commission_statement.commissionAmount as commissionAmount')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'view_dealer_commission_statement.dealerId')
                    ->orWhere(function($query) use($fromDate, $toDate, $dealer) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('view_dealer_commission_statement.date', array($fromDate, $toDate));
                        }

                        if ($dealer) {
                            $query->whereIn('view_dealer_commission_statement.dealerId', $dealer);
                        }
                    })
                    ->where('view_dealer_commission_statement.companyId', $this->company)
//                    ->where('view_dealer_commission_statement.showroomId', $this->showroomId)
                    ->get();
        } else {
            $dealerCommissionStatements = "";
        }

        $pdf = PDF::loadView('admin.dealerCommissionStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'btnPrintSummary' => $btnPrintSummary, 'btnPrintRecord' => $btnPrintRecord, 'dealerCommissionSummaries' => $dealerCommissionSummaries, 'dealerCommissionStatements' => $dealerCommissionStatements]);


        $SummaryFileName = "dealer_commission_summary_". $fromDate . "_to_" . $toDate .".pdf";
        $DetailsFileName = "dealer_commission_history_". $fromDate . "_to_" . $toDate .".pdf";

        // dd($request->btnPrintSummary);

        $file_name = $request->btnPrintSummary == "Print Summary" ? $SummaryFileName : $DetailsFileName;

        return $pdf->stream($file_name);


        // return $pdf->stream('dealer_commission_statement_' . $fromDate . '_to_' . $toDate . '.pdf');

    }

}
