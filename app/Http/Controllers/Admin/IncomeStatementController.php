<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use Auth;
use App\Product;
use App\CoaSetup;
use App\StoreSetup;
use App\Helper\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class IncomeStatementController extends Controller {

    public function index(Request $request) {
        $title = "Income Statement";
        $searchFormLink = "incomeStatement.index";
        $printFormLink = "incomeStatement.print";
        $print = $request->print;

        $storeId = '';

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $data = [];
        
        $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        
        $companyStores = array_merge([$this->company], $storeIds);

        $selectedProducts = Product::where('company_id', $this->company)->where('showroom_id', $this->showroomId)->get();

        $closingStockValue = 0;
        $openingBalance = 0;

        foreach ($selectedProducts as $selectedproduct) {

            $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

            $closingStockValue += $stock->stockValue();

            $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

            $date = Carbon::parse($fromDate);
            $openingDate = $date->subDays(1);
            $openingStock = new Stock($openingDate, $openingDate, $selectedproduct->id, $companyStores);
            $openingBalance += $openingStock->stockValue();
        }


        $incomeLists = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'I')
                ->groupBy('tbl_account_transactions.coa_head_code', 'tbl_coa.head_code')
                ->get();

        $expenseList1 = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'E')
                ->where('tbl_coa.general_ledger', 1)
                ->where('tbl_coa.transaction', 1)
                ->groupBy('tbl_account_transactions.coa_head_code', 'tbl_coa.head_code')
                ->get();

        foreach ($expenseList1 as $val) {
            $ld = [
                'headCode' => $val->headCode,
                'headName' => $val->headName,
                'amount' => $val->amount
            ];

            array_push($data, $ld);
        }

        $expenseList2 = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.parent_head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'E')
                ->where('tbl_coa.general_ledger', 0)
                ->where('tbl_coa.transaction', 1)
                ->groupBy('tbl_coa.parent_head_name')
                ->get();

        foreach ($expenseList2 as $val2) {
            $ld = [
                'headCode' => $val2->headCode,
                'headName' => $val2->headName,
                'amount' => $val2->amount
            ];

            array_push($data, $ld);
        }

        return view('admin.incomeStatement.index')->with(compact('closingStockValue', 'title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'incomeLists', 'data', 'openingBalance'));
    }

    public function print(Request $request) {
        $title = "Print Income Statement";
        $print = $request->print;

        $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        
        $companyStores = array_merge([$this->company], $storeIds);

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $data = [];
        $incomeLists = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'I')
                ->groupBy('tbl_account_transactions.coa_head_code', 'tbl_coa.head_code')
                ->get();

        $selectedProducts = Product::where('company_id', $this->company)->where('showroom_id', $this->showroomId)->get();

        $closingStockValue = 0;
        $openingBalance = 0;

        foreach ($selectedProducts as $selectedproduct) {

            $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

            $closingStockValue += $stock->stockValue();

            $date = Carbon::parse($fromDate);
            $openingDate = $date->subDays(1);
            $openingStock = new Stock($openingDate, $openingDate, $selectedproduct->id, $companyStores);
            $openingBalance += $openingStock->stockValue();
        }

        $expenseList1 = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'E')
                ->where('tbl_coa.general_ledger', 1)
                ->where('tbl_coa.transaction', 1)
                ->groupBy('tbl_account_transactions.coa_head_code', 'tbl_coa.head_code')
                ->get();

        foreach ($expenseList1 as $val) {
            $ld = [
                'headCode' => $val->headCode,
                'headName' => $val->headName,
                'amount' => $val->amount
            ];

            array_push($data, $ld);
        }

        $expenseList2 = DB::table('tbl_account_transactions')
                ->select('tbl_account_transactions.coa_head_code as headCode', 'tbl_coa.parent_head_name as headName', DB::raw('(SUM(tbl_account_transactions.debit_amount) - SUM(tbl_account_transactions.credit_amount)) as amount'))
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.approve', 1)
                ->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate))
                ->where('tbl_coa.head_type', 'E')
                ->where('tbl_coa.general_ledger', 0)
                ->where('tbl_coa.transaction', 1)
                ->groupBy('tbl_coa.parent_head_name')
                ->get();

        foreach ($expenseList2 as $val2) {
            $ld = [
                'headCode' => $val2->headCode,
                'headName' => $val2->headName,
                'amount' => $val2->amount
            ];

            array_push($data, $ld);
        }

        $pdf = PDF::loadView('admin.incomeStatement.print', ['closingStockValue' => $closingStockValue, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'incomeLists' => $incomeLists, 'data' => $data, 'openingBalance' => $openingBalance], [], ['orientation' => 'P']);
        $pdf->stream('income_statement_' . $fromDate . '_' . $toDate . '.pdf');
    }

}
