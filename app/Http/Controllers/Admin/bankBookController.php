<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class bankBookController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Bank Book";
        $searchFormLink = "bankBook.index";
        $printFormLink = "bankBook.print";
        $print = $request->print;

        $coaHeadCode = CoaSetup::where('company_id', $this->company)->where('head_name', 'Cash At Bank')->first();

        $bankBookLists = CoaSetup::where('company_id', $this->company)->where('transaction', '1')->where('head_code', 'LIKE', '%' . $coaHeadCode->head_code . '%')->get();

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $bankBookHead = $request->bankBookHead;

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', $bankBookHead)
                ->where('approve', 1)
                ->first();

        $bankBooksReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', $bankBookHead)
                ->where('approve', 1)
                ->orderby('voucher_no')
                ->get();

        return view('admin.bankBook.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'bankBookLists', 'bankBooksReports', 'bankBookHead', 'previousBalance'));
    }

    public function print(Request $request) {
        // dd($request->all());
        $title = "Bank Book";
        $searchFormLink = "bankBook.index";
        $printFormLink = "bankBook.print";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $bankBookHead = $request->bankBookHead;

        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', $bankBookHead)
                ->where('approve', 1)
                ->first();

        $bankBooksReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', $bankBookHead)
                ->where('approve', 1)
                ->orderby('voucher_no')
                ->get();

        $pdf = PDF::loadView('admin.bankBook.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'bankBooksReports' => $bankBooksReports, 'previousBalance' => $previousBalance], [], ['orientation' => 'P']);
        $pdf->stream('bank_book_' . $fromDate . '_' . $toDate . '.pdf');
    }

}
