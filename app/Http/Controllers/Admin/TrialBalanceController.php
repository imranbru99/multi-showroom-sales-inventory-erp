<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class TrialBalanceController extends Controller {

    public function index(Request $request) {
        $title = "Trial Balance";
        $searchFormLink = "trialBalance.index";
        $printFormLink = "trialBalance.print";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $coaLists = CoaSetup::where('company_id', $this->company)->where('general_ledger', 1)->get();

        return view('admin.trialBalance.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'coaLists'));
    }

    public function print(Request $request) {
        $title = "Print Trial Balance";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $coaLists = CoaSetup::where('company_id', $this->company)->where('general_ledger', 1)->get();

        $pdf = PDF::loadView('admin.trialBalance.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'coaLists' => $coaLists], [], ['orientation' => 'P']);
        $pdf->stream('trial_balance_' . $date . '.pdf');
    }

}
