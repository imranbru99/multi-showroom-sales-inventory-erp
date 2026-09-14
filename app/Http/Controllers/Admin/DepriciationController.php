<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class DepriciationController extends Controller {

    public function index(Request $request) {
        $title = "Depreciation";
        $searchFormLink = "depriciation.index";
        $printFormLink = "depriciation.print";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));

        $fixAssets = $this->assets($fromDate, $toDate, $lastDate, 'Fixed Assets');

//        dd($fixAssets);

        return view('admin.depriciation.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'fixAssets'));
    }

    public function print(Request $request) {
        $title = "Depreciation";
        $print = $request->print;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));


        $fixAssets = $this->assets($fromDate, $toDate, $lastDate, 'Fixed Assets');


        $pdf = PDF::loadView('admin.depriciation.print', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'fixAssets' => $fixAssets,
                        ], [], ['orientation' => 'P']);
        $pdf->stream('depreciation' . $fromDate . '_' . $toDate . '.pdf');
    }

    public function getAmount($fromDate, $toDate, $lastDate, $headCode) {
        $balance = 0;
        $headReports = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.*', 'tbl_coa.head_type')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('voucher_date', array($fromDate, $toDate))
                ->where('coa_head_code', 'LIKE', $headCode . '%')
                ->where('approve', 1)
                ->get();

        foreach ($headReports as $headReport) {
            if ($headReport->head_type == 'I' || $headReport->head_type == 'L') {
                $balance += $headReport->credit_amount - $headReport->debit_amount;
            } else {
                $balance += $headReport->debit_amount - $headReport->credit_amount;
            }
        }
        return $balance;
    }

    private function assetPrevious($lastDate, $headCode) {
        $previousBalance = DB::table('tbl_account_transactions')
                ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as previousBalance'))
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_date', '<=', $lastDate)
                ->where('coa_head_code', 'LIKE', $headCode . '%')
                ->where('approve', 1)
                ->first();

        return $previousBalance->previousBalance;
    }

    public function assets($fromDate, $toDate, $lastDate, $type) {
        $childs = CoaSetup::select('head_name', 'head_code')
                ->where('company_id', $this->company)
                ->where('parent_head_name', $type)
                ->get();
        $childInfo = [];
        foreach ($childs as $child) {
            $previousBalance = $this->assetPrevious($lastDate, $child->head_code);
            if ($child->head_name == 'Furniture & Fixtures') {
                $previous = $previousBalance - ($previousBalance * 0.15);
            } else {
                $previous = $previousBalance - ($previousBalance * 0.20);
            }

            $childInfo[] = [
                'name' => $child->head_name,
                'previous' => $previous,
                'amount' => $this->getAmount($fromDate, $toDate, $lastDate, $child->head_code)
            ];
        }


        return $childInfo;
    }

}
