<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use PDF;

class VoucherListController extends Controller {

    public function index(Request $request) {
        $title = "Voucher List";
        $searchFormLink = "voucherList.index";
        $printFormLink = "voucherList.print";
        $print = $request->print;

        if ($print) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $voucherLists = DB::table('view_voucher_approve')
                    ->orWhere(function ($query) use ($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('date', array($fromDate, $toDate));
                        }
                    })
                    ->where('showroomId', $this->showroomId)
                    ->where('approve', '1')
                    ->get();
        } else {
            $voucherLists = DB::table('view_voucher_approve')->where('showroomId', $this->showroomId)->where('approve', '1')->get();
            $fromDate = "";
            $toDate = "";
        }

        return view('admin.voucherList.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'voucherLists'));
    }

    public function print(Request $request) {
        $title = "Voucher List";
        $print = $request->print;

        if ($request->fromDate != "" && $request->toDate != "") {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $voucherLists = DB::table('view_voucher_approve')
                    ->orWhere(function ($query) use ($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('date', array($fromDate, $toDate));
                        }
                    })
                    ->where('showroomId', $this->showroomId)
                    ->where('approve', '1')
                    ->get();
        } else {
            $voucherLists = DB::table('view_voucher_approve')->where('showroomId', $this->showroomId)->where('approve', '1')->get();
            $fromDate = "";
            $toDate = "";
        }

        $pdf = PDF::loadView('admin.voucherList.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'voucherLists' => $voucherLists], [], ['orientation' => 'P']);
        $pdf->stream('voucher_list_' . $fromDate . '_' . $toDate . '.pdf');
    }

}
