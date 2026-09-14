<?php

namespace App\Services\Product;

use App\Product;
use App\RetailSale;
use App\RetailSales;
use App\ProductIssue;
use App\ProductIssueList;
use App\CustomerRegistrationSetup;
use Illuminate\Support\Collection;

class Report
{

    public static function Report($startDate, $endDate, $reference = null, $category = null, $fproduct = null)
    {

        $issueList = ProductIssue::with(['products.product.category', 'dealer', 'showroom'])
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate);


        if ($category) {
            $issueList = $issueList->whereHas('products.product', function ($q) use ($category) {
                $q->where('category_id', $category);
            });
        }

        if ($reference) {
            $issueList = $issueList->whereIn('sales_by', $reference);
        }

        if ($fproduct) {
            $issueList = $issueList->whereHas('product.product', function ($q) use ($fproduct) {
                $q->where('id', $fproduct);
            });
        }

        $issueList = $issueList->get();


        $data = [];

        $sl = 1;
        foreach ($issueList as $il) {

            foreach ($il->products as $p) {

                $ld = [

                    'sl' => $sl,
                    'date' => date('d-m-Y', strtotime($il->date)),
                    'dealer_name' => @$il->dealer->name,
                    'dealer_phone' => @$il->dealer->mobile,
                    'showroom' => @$il->showroom->name,
                    'category' => @$p->product->category->name,
                    'product_name' => @$p->product->name,
                    'product_serial' => @$p->serial_no,
                    'product_model' => @$p->model_no,
                    'product_color' => @$p->product->color,
                    'product_price' => @$p->amount,

                ];
            }


            array_push($data, $ld);
            $sl++;
        }

        return $data;
    }


    public static function RetailReport($startDate, $endDate, $employee = null, $category = null, $fproduct = null)
    {


        $issueList = RetailSale::with(['products.product.category', 'seller'])
            ->where('sale_date', '>=', $startDate)
            ->where('sale_date', '<=', $endDate)
            ->orderBy('sale_date', 'asc');

        if ($employee) {
            $issueList = $issueList->where('reference_id', $employee);
        }

        $issueList = $issueList->get();

        // dd($issueList[0]);

        $data = [];

        $sl = 1;

        foreach ($issueList as $il) {

            $cstmer = CustomerRegistrationSetup::where('id', $il->customer_id)->first();

            $products = $il->products;

            foreach ($products as $p) {

                // dd($p);

                $qty = $p->qty;

                if ($il->sale_type == 'Cash') {
                    $price = $p->cash_price;
                } else if ($il->sale_type == 'Short Installment') {
                    $price = $p->mrp_price;
                } else {
                    $price = $p->hire_price;
                }


                $ld = [

                    'sl' => $sl,
                    'date' => date('d-m-Y', strtotime($il->sale_date)),
                    'account_no' => @$cstmer->code,
                    'customer_name' => @$cstmer->name,
                    'customer_phone' => @$cstmer->phone_no,
                    // 'customer_nid' => @$cstmer->nid,
                    'memo_no' => @$il->invoice_no,
                    // 'category' => @$p->product->category->name,
                    'product_name' => @$p->product->name,
                    'product_serial' => @$p->product_serial,
                    'product_model' => @$p->product->model_no,
                    'seller' => @$il->seller->name,
                    'product_qty' => @$p->qty,
                    'product_price' => @$price,
                    'product_discount' => @$p->discount,
                    'product_gift_voucher' => @$p->gift_voucher,
                    'product_exchange_crt' => @$p->exchange_crt,
                    'total_product_price' => (@$p->qty * @$price) - (@$p->discount + @$p->gift_voucher + @$p->exchange_crt),

                ];

                // dd($ld);

                array_push($data, $ld);

                $sl++;
            }
        }

        // dd($data);

        return $data;
    }


    public static function summaryReport($startDate, $endDate, $reference = null, $category = null, $fproduct = null)
    {
        $products = Product::query();

        if ($category) {
            $products = $products->where('category_id', $category);
        }

        if ($fproduct) {
            $products = $products->where('id', $fproduct);
        }

        $products = $products->get();

        $data = [];

        $sl = 1;
        foreach ($products as $product) {

            $ld = [
                'sl' => $sl,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_model' => $product->model_no,
                'product_sold_data' => self::productSoldDataSummary($startDate, $endDate, $reference, $product),
            ];

            if ($ld['product_sold_data']['totalQty'] > 0) {
                array_push($data, $ld);
                $sl++;
            }
        }

        return $data;
    }


    public static function retailSummaryReport($startDate, $endDate, $employee = null, $category = null, $fproduct = null)
    {
        $retailSales = RetailSale::with(['customer', 'products'])
            ->where('sale_date', '>=', $startDate)
            ->where('sale_date', '<=', $endDate);

        if ($employee) {
            $retailSales = $retailSales->where('reference_id', $employee);
        }

        $retailSales = $retailSales->get();

        $data = [];

        $sl = 1;

        foreach ($retailSales as $retailSale) {

            $rsp = RetailSales::where('sale_id', $retailSale)->get();

            $totalAmount = 0;
            $totalQty = 0;

            foreach ($retailSale->products as $saleProduct) {

                $totalQty += $saleProduct->qty;
                $totalAmount += $saleProduct->qty * $saleProduct->cash_price;
            }


            $ld = [
                'sl' => $sl,
                'account_no' => @$retailSale->customer->code,
                'customer_name' => @$retailSale->customer->name,
                'customer_phone' => @$retailSale->customer->phone_no,
                'qty' => $totalQty,
                'total_amount' => $totalAmount,
            ];


            if ($ld['qty'] > 0) {
                array_push($data, $ld);
                $sl++;
            }
        }

        return $data;
    }

    public static function productSoldDataSummary($startDate, $endDate, $reference = null, $product)
    {

        $productsale = ProductIssueList::with('issue')
            ->whereHas('issue', function ($q) use ($startDate, $endDate, $reference) {

                $q->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate);

                if ($reference) {
                    $q = $q->where('sales_by', $reference);
                }
            });

        $productsale = $productsale->where('product_id', $product->id)->get();

        return [
            'totalQty' => $productsale->sum('qty'),
            'totalAmount' => $productsale->sum('amount'),
        ];
    }
}
