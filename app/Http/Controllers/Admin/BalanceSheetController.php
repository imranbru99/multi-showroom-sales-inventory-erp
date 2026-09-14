<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use App\Product;
use App\StoreSetup;
use App\Helper\Stock;
use Auth;
use DB;
use PDF;
use Carbon\Carbon;

class BalanceSheetController extends Controller {

    public function index(Request $request) {
        $title = "Balance Sheet";
        $searchFormLink = "balanceSheet.index";
        $printFormLink = "balanceSheet.print";
        $print = $request->print;

        $fromDate = date('1971-01-01', strtotime($request->fromDate));
        $toDate = date('Y-m-d');
        $lastDate = '1971-01-01';


        $storeIds = StoreSetup::where('company_id', $this->company)
                ->get()
                ->pluck('id')
                ->toArray();

        $companyStores = array_merge([$this->company], $storeIds);

        $currentFix = $this->assets($fromDate, $toDate, $lastDate, 'Assets');
        $liabilities = $this->assets($fromDate, $toDate, $lastDate, 'Liabilities');

        $closingStock = 0;
        $openingBalance = 0;
        $OclosingStock = 0;

        $allProducts = Product::where('company_id', $this->company)->where('showroom_id', $this->showroomId)->select('id')->get();
        foreach ($allProducts as $allProduct) {
            $stock = new Stock($fromDate, $toDate, $allProduct->id, $companyStores);
            $closingStock += $stock->stockValue();
            $openingStock = new Stock($lastDate, $lastDate, $allProduct->id, $companyStores);
            $openingBalance += $openingStock->stockValue();

            $Ostock = new Stock('2019-01-01', $lastDate, $allProduct->id, $companyStores);
            $OclosingStock += $Ostock->stockValue();
            // $OopeningBalance += $openingStock->stockValue();
        }

        $opningPF = $this->profitLoss(0, $OclosingStock, '2019-01-01', $lastDate);
        $currentPF = $this->profitLoss($openingBalance, $closingStock, $fromDate, $toDate);

        $profitLoss = [
            'opening' => number_format($opningPF, 2, '.', ''),
            'current' => number_format($currentPF, 2, '.', ''),
        ];


        return view('admin.balanceSheet.index')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'fromDate',
                                'toDate',
                                'currentFix',
                                'closingStock',
                                'liabilities',
                                'profitLoss'
        ));
    }

    public function print(Request $request) {
        $title = "Balance Sheet";
        $print = $request->print;

        $fromDate = date('1971-01-01', strtotime($request->fromDate));
        $toDate = date('Y-m-d');
        $lastDate = '1971-01-01';


        $storeIds = StoreSetup::where('company_id', $this->company)
                ->get()
                ->pluck('id')
                ->toArray();

        $companyStores = array_merge([$this->company], $storeIds);


        $currentFix = $this->assets($fromDate, $toDate, $lastDate, 'Assets');
        $liabilities = $this->assets($fromDate, $toDate, $lastDate, 'Liabilities');

        $closingStock = 0;
        $openingBalance = 0;
        $OclosingStock = 0;

        $allProducts = Product::where('company_id', $this->company)->where('showroom_id', $this->showroomId)->select('id')->get();
        foreach ($allProducts as $allProduct) {
            $stock = new Stock($fromDate, $toDate, $allProduct->id, $companyStores);
            $closingStock += $stock->stockValue();
            $openingStock = new Stock($lastDate, $lastDate, $allProduct->id, $companyStores);
            $openingBalance += $openingStock->stockValue();

            $Ostock = new Stock('2019-01-01', $lastDate, $allProduct->id, $companyStores);
            $OclosingStock += $Ostock->stockValue();
            // $OopeningBalance += $openingStock->stockValue();
        }

        $opningPF = $this->profitLoss(0, $OclosingStock, '2019-01-01', $lastDate);
        $currentPF = $this->profitLoss($openingBalance, $closingStock, $fromDate, $toDate);

        $profitLoss = [
            'opening' => number_format($opningPF, 2, '.', ''),
            'current' => number_format($currentPF, 2, '.', ''),
        ];


        $pdf = PDF::loadView('admin.balanceSheet.print', [
                    'title' => $title, 'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'print' => $print,
                    'currentFix' => $currentFix,
                    'liabilities' => $liabilities,
                    'closingStock' => $closingStock,
                    'profitLoss' => $profitLoss,
                    'profitLoss' => $profitLoss,
                        ], [], ['orientation' => 'L']);
        $pdf->stream('balance_sheet_' . $fromDate . '_' . $toDate . '.pdf');
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

        $previousBalance = $this->assetPrevious($lastDate, $headCode);
        foreach ($headReports as $headReport) {
            if ($headReport->head_type == 'I' || $headReport->head_type == 'L') {
                $balance += $headReport->credit_amount - $headReport->debit_amount;
            } else {
                $balance += $headReport->debit_amount - $headReport->credit_amount;
            }
        }
        return $balance + $previousBalance;
    }

    private function profitLoss($openingBalance, $closingStockValue, $fromDate, $toDate) {
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

        $totalIncome = 0;
        $totalExpanse = 0;
        foreach ($incomeLists as $incomeList) {
            $totalIncome += abs($incomeList->amount);
        }

        foreach ($data as $d) {
            $totalExpanse += $d['amount'];
        }

        // $totalExpanse = $totalExpanse >= 0 ? ($totalExpanse + $openingBalance) : $totalExpanse;
        $netIncome = $totalIncome + $closingStockValue;
        $profitLoss = $netIncome - ($totalExpanse + $openingBalance);

        return $profitLoss;
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
        $parents = CoaSetup::select('head_name')
                ->where('parent_head_name', $type)
                ->where('company_id', $this->company)
                ->orderBy('head_name', 'asc')
                ->get();
        $info = [];
        foreach ($parents as $parent) {
            $childs = CoaSetup::select('head_name', 'head_code')
                    ->where('parent_head_name', $parent->head_name)
                    ->where('company_id', $this->company)
                    ->get();
            $childInfo = [];
            foreach ($childs as $child) {
                $childInfo[] = [
                    'headName' => $parent->head_name,
                    'name' => $child->head_name,
                    'amount' => $this->getAmount($fromDate, $toDate, $lastDate, $child->head_code)
                ];
            }

            $info[] = [
                'head' => $parent->head_name,
                'childs' => $childInfo
            ];
        }


        return $info;
    }

}
