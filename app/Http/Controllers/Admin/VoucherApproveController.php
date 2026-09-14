<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VoucherApprove;
use DB;

class VoucherApproveController extends Controller {

    public function index(Request $request) {
        $title = "Voucher Approves";
        $searchFormLink = 'voucherApprove.index';
        $print = $request->print;

        if ($request->fromDate) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $voucherLists = VoucherApprove::with('showroom')
                    ->where('approve', 0)
                    ->where('showroom_id', $this->showroomId)
                    ->where('voucher_date', '>=', $fromDate)
                    ->where('voucher_date', '<=', $toDate)
                    ->groupBy('voucher_no')
                    ->orderBy('voucher_date', 'desc')
                    ->get();
        } else {
            $voucherLists = [];
            $fromDate = "";
            $toDate = "";
        }

        return view('admin.voucherApprove.index')->with(compact('title', 'searchFormLink', 'print', 'fromDate', 'toDate', 'voucherLists'));
    }

    public function view($voucherApproveId) {
        $title = "View Account Voucher Details";
        $accountTransaction = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*', 'tbl_showroom.name as showroomName', 'tbl_coa.head_name as accountHeadName')
                ->leftJoin('tbl_showroom', 'tbl_showroom.id', '=', 'tbl_account_transactions.showroom_id')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                 ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.id', $voucherApproveId)
                ->first();

        $voucherType = $accountTransaction->voucher_type;

        $accountTransactionLists = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*', 'tbl_coa.head_name as accountHeadName')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                ->where('voucher_no', $accountTransaction->voucher_no)
                ->where('voucher_type', $voucherType)
                ->where(function ($query) use ($voucherType) {
                    if ($voucherType == "DV") {
                        $query->where('credit_amount', 0);
                    }

                    if ($voucherType == "CV") {
                        $query->where('debit_amount', 0);
                    }
                })
                ->orderBy('tbl_account_transactions.id', 'asc')
                ->get();

        return view('admin.voucherApprove.view')->with(compact('title', 'accountTransaction', 'accountTransactionLists'));
    }

    //previous approve change by tanmoy
    // public function approve(Request $request)
    // {
    //     $voucherApproveId = $request->voucherApproveId;
    //     $view = $request->view;
    //     $accountTransaction = VoucherApprove::find($voucherApproveId);
    //     if ($accountTransaction->approve == 0)
    //     {
    //         VoucherApprove::where('voucher_no',$accountTransaction->voucher_no)->update(['approve'=>1,'approve_by'=>$this->userId]);
    //     }
    //     else
    //     {
    //         VoucherApprove::where('voucher_no',$accountTransaction->voucher_no)->update(['approve'=>0,'approve_by'=>$this->userId]);
    //     }
    //     if ($view)
    //     {
    //         return redirect(route('voucherApprove.view',$voucherApproveId));
    //         // return view('admin.voucherApprove.view')->with(compact('title','accountTransaction','accountTransactionLists'));
    //     }
    // }
    //previous approve change by tanmoy

    public function approve(Request $request) {
        $voucherApproveId = $request->voucherApproveId;
        $view = $request->view;

        $accountTransaction = VoucherApprove::find($voucherApproveId);

        if ($accountTransaction) { //change by tanmoy
            if ($accountTransaction->approve == 0) {
                VoucherApprove::where('voucher_no', $accountTransaction->voucher_no)->update(['approve' => 1, 'approve_by' => $this->userId]);
            } else {
                VoucherApprove::where('voucher_no', $accountTransaction->voucher_no)->update(['approve' => 0, 'approve_by' => $this->userId]);
            }

            if ($view) {
                return redirect(route('voucherApprove.view', $voucherApproveId));
                // return view('admin.voucherApprove.view')->with(compact('title','accountTransaction','accountTransactionLists'));
            }
        }

        return redirect(route('voucherApprove.index')); //change by tanmoy
    }

}
