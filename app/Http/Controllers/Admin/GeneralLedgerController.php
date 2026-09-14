<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class GeneralLedgerController extends Controller {

    public function index(Request $request) {
        $title = "General Ledger";
        $searchFormLink = "generalLedger.index";
        $printFormLink = "generalLedger.print";
        $print = $request->print;

        $generalLedgerLists = CoaSetup::where('company_id', $this->company)->where('general_ledger', '1')->get();

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $generalLedgerHead = $request->generalLedgerHead;

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', 'LIKE', $generalLedgerHead . '%')
                ->where('approve', 1)
                ->first();

        $generalLedgerReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*', 'tbl_coa.head_level as head_level', 'tbl_coa.head_code as head_code')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', 'LIKE', $generalLedgerHead . '%')
                ->where('approve', 1)
                ->orderby('voucher_date')
                ->get();

        return view('admin.generalLedger.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'generalLedgerLists', 'generalLedgerReports', 'generalLedgerHead', 'previousBalance'));
    }

    public function print(Request $request) {
        $title = "Print General Ledger";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $generalLedgerHead = $request->generalLedgerHead;

        $generalLedgerHeadName = CoaSetup::where('company_id', $this->company)->where('head_code', $generalLedgerHead)->first();

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', 'LIKE', $generalLedgerHead . '%')
                ->where('approve', 1)
                ->first();

        $generalLedgerReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*', 'tbl_coa.head_level as head_level', 'tbl_coa.head_code as head_code')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', 'LIKE', $generalLedgerHead . '%')
                ->where('approve', 1)
                ->orderby('voucher_date')
                ->get();

        $pdf = PDF::loadView('admin.generalLedger.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'generalLedgerReports' => $generalLedgerReports, 'generalLedgerHeadName' => $generalLedgerHeadName, 'previousBalance' => $previousBalance], [], ['orientation' => 'P']);
        $pdf->stream('general_ledger_list_' . $fromDate . '_' . $toDate . '.pdf');
    }

}
