<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class TransactionLedgerController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Transaction Ledger";
        $searchFormLink = "transactionLedger.index";
        $printFormLink = "transactionLedger.print";
        $print = $request->print;

        $transactionLedgerLists = CoaSetup::where('company_id', $this->company)->where('transaction', '1')->get();

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $transactionLedgerHead = $request->transactionLedgerHead;

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', $transactionLedgerHead)
                ->where('approve', 1)
                ->first();

        $transactionLedgerReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', $transactionLedgerHead)
                ->where('approve', 1)
                ->orderby('voucher_no')
                ->get();

        return view('admin.transactionLedger.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'transactionLedgerLists', 'transactionLedgerReports', 'transactionLedgerHead', 'previousBalance'));
    }

    public function print(Request $request) {
        $title = "Print Transaction Ledger";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $transactionLedgerHead = $request->transactionLedgerHead;

        $transactionLedgerHeadName = CoaSetup::where('company_id', $this->company)->where('head_code', $transactionLedgerHead)->first();

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', $transactionLedgerHead)
                ->where('approve', 1)
                ->first();

        $transactionLedgerReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', $transactionLedgerHead)
                ->where('approve', 1)
                ->orderby('voucher_date')
                ->get();

        $pdf = PDF::loadView('admin.transactionLedger.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'transactionLedgerReports' => $transactionLedgerReports, 'transactionLedgerHeadName' => $transactionLedgerHeadName, 'previousBalance' => $previousBalance], [], ['orientation' => 'P']);
        $pdf->stream('transaction_ledger_' . $fromDate . '_' . $toDate . '.pdf');
    }

}
