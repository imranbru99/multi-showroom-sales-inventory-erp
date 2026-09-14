<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Product;
use App\StaffSetup;
use App\RetailSales;
use App\CommissionPercentage;
use DB;
use PDF;

class CashComHistoController extends Controller
{
    public function index(Request $request)
    {
        $title = "Cash Commission History";
        $searchFormLink = "cashComHis.index";
        $printFormLink = "cashComHis.print";

        $month = $request->month;

        $fromDate = date('Y-m-d', strtotime($request->month . '-01'));
        $toDate = date('Y-m-t', strtotime($fromDate));

        $employeeId = $request->employee;
        $print = $request->print;
        $type = $request->type;

        $employees = StaffSetup::where('status', 1)->get();

        $title = '';
        $cashData = [];
        if ($print) {
            if ($type == 1) {
                $title = 'Cash Commission History With Discount';
                $cashData = $this->cashDiscountData($fromDate, $toDate, $employeeId);
            } else {
                $title = 'Cash Commission History Without Discount';
                $cashData = $this->cashWithoutDiscountData($fromDate, $toDate, $employeeId);
            }
        }

        return view('admin.cashComHis.index')->with(compact(
            'title',
            'searchFormLink',
            'printFormLink',
            'print',
            'month',
            'fromDate',
            'toDate',
            'employees',
            'employeeId',
            'type',
            'cashData'
        ));
    }

    public function print(Request $request)
    {
        $fromDate = date('Y-m-d', strtotime($request->month . '-01'));
        $toDate = date('Y-m-t', strtotime($fromDate));

        $employeeId = $request->employee;
        $print = $request->print;
        $type = $request->type;
        $month = $request->month;

        $title = '';
        $cashData = [];
        if ($print) {
            if ($type == 1) {
                $title = 'Cash Commission History With Discount';
                $cashData = $this->cashDiscountData($fromDate, $toDate, $employeeId);
            } else {
                $title = 'Cash Commission History Without Discount';
                $cashData = $this->cashWithoutDiscountData($fromDate, $toDate, $employeeId);
            }
        }


        $pdf = PDF::loadView('admin.cashComHis.print', compact(
            'title',
            'month',
            'employeeId',
            'type',
            'cashData'
        ));

        return $pdf->stream('cash_commission_history.pdf');
    }



    public function cashWithoutDiscountData($fromDate, $toDate, $employeeId)
    {
        $showrromId = $this->showroomId;
        $retailSales = RetailSales::with('sale', 'sale.customer', 'sale.seller')
            ->whereHas('sale', function ($q) use ($fromDate, $toDate, $showrromId) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('sale_type', 'cash')
                    ->where('showroom_id', $showrromId);
            })
            ->whereHas('sale.seller', function ($q) use ($employeeId) {
                if ($employeeId) {
                    $q->where('reference_id', $employeeId);
                }
            })
            ->get();


        $commissionPercentage = CommissionPercentage::first();

        $data = [];
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            $total_sales = $sale->sales_price;
            $total_discount = $sale->discount;
            $total_voucher = $sale->gift_voucher;
            $total_excrt = $sale->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            if ($product->price <= $actual_sales) {

                $price = $product->price;
                if ($product->price == 0) {
                    $price = 1;
                }

                $profitLoss = 100;
                $commissionPercent = 100;
                $commissionAmount = $actual_sales;
                $employeeCom = $commissionAmount * ($commissionPercentage->r_cash / 100);
                // $employeeCom = $actual_sales * ($commissionPercentage->cash / 100);


                $data[] = [
                    'date' => date('d-m-Y', strtotime($sale->sale->sale_date)),
                    'invoice_no' => $sale->sale->invoice_no,
                    'ac_no' => $sale->sale->customer->code,
                    'client_name' => $sale->sale->customer->name,
                    'phone_no' => $sale->sale->customer->phone_no,
                    'address' => $sale->sale->customer->present_address,
                    'reference_by' => $sale->sale->seller->name,
                    'purchase_com' => '16.80%',
                    'cash_price' => number_format($product->price, 2, '.', ''),
                    'sales_price' => number_format($actual_sales, 2, '.', ''),
                    'profit_loss' => number_format($profitLoss, 2, '.', ''),
                    'commission_percent' => number_format($commissionPercent, 2, '.', ''),
                    'commission_amount' => number_format($commissionAmount, 2, '.', ''),
                    'employee_commission' => number_format($employeeCom, 2, '.', ''),
                ];
            }
        }
        return $data;
    }


    public function cashDiscountData($fromDate, $toDate, $employeeId)
    {
        $showrromId = $this->showroomId;
        $retailSales = RetailSales::with('sale', 'sale.customer', 'sale.seller')
            ->whereHas('sale', function ($q) use ($fromDate, $toDate, $showrromId) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('sale_type', 'cash')
                    ->where('showroom_id', $showrromId);
            })
            ->whereHas('sale.seller', function ($q) use ($employeeId) {
                if ($employeeId) {
                    $q->where('reference_id', $employeeId);
                }
            })
            ->get();


        $commissionPercentage = CommissionPercentage::first();

        $data = [];
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            $total_sales = $sale->sales_price;
            $total_discount = $sale->discount;
            $total_voucher = $sale->gift_voucher;
            $total_excrt = $sale->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            if ($product->price > $actual_sales) {

                $price = $product->price;
                if ($product->price == 0) {
                    $price = 1;
                }

                $profitLoss = 100 - (($actual_sales * 100) / $price);
                $commissionPercent = 16.8 - $profitLoss;
                // $commissionAmount = ($actual_sales * $commissionPercent) / 100;
                $commissionAmount = ($price * $commissionPercent) / 100;
                // $employeeCom = $price * ($commissionPercentage->cash / 100);
                $employeeCom = ($price * ($commissionPercentage->r_cash / 100) *  $commissionPercent) / 16.80;




                $data[] = [
                    'date' => date('d-m-Y', strtotime($sale->sale->sale_date)),
                    'invoice_no' => $sale->sale->invoice_no,
                    'ac_no' => $sale->sale->customer->code,
                    'client_name' => $sale->sale->customer->name,
                    'phone_no' => $sale->sale->customer->phone_no,
                    'address' => $sale->sale->customer->present_address,
                    'reference_by' => $sale->sale->seller->name,
                    'purchase_com' => '16.80%',
                    'cash_price' => number_format($product->price, 2, '.', ''),
                    'sales_price' => number_format($actual_sales, 2, '.', ''),
                    'profit_loss' => number_format($profitLoss, 2, '.', ''),
                    'commission_percent' => number_format($commissionPercent, 2, '.', ''),
                    'commission_amount' => number_format($commissionAmount, 2, '.', ''),
                    'employee_commission' => number_format($employeeCom, 2, '.', ''),
                ];
            }
        }
        return $data;
    }
}
