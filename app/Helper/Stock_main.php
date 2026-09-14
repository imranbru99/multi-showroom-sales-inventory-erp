<?php

namespace App\Helper;

use App\Product;
use App\LiftingProduct;
use App\ProductIssueList;
use App\LiftingReturnProduct;
use DB;

class Stock
{
    public $openingStart = '0000-00-00';
    public $startDate = 0;
    public $endDate = 0;
    public $productId = 0;

    public function __construct($startDate, $endDate, $productId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->productId = $productId;
    }


    public function sales()
    {
        $product_id = $this->productId;

        $fromDate = $this->startDate;
        $toDate = $this->endDate;

        // get dealer sales
        $dealerSalesLineProduct = ProductIssueList::with('issue')
            ->whereHas('issue', function ($query) use ($fromDate, $toDate) {
                $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate);
            })
            ->where('product_id', $product_id)
            ->get();

        $dealerSalesLineProduct = $dealerSalesLineProduct->sum('qty');

        $retailSales = 0;

        return $totalSales = $dealerSalesLineProduct + $retailSales;
    }

    public function salesReturn()
    {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
       
         $salesReturn = DB::table('tbl_sales_return')
                ->orWhere(function($query) use($fromDate, $toDate){
                      $query->where('return_date', '>=', $fromDate)
                    ->where('return_date', '<=', $toDate); 
                })
                ->where('product_id', $product_id)
                ->get();
    return $salesReturn->sum('qty');
    }

    public function lifting()
    {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;

        // lifting start

        $lifting = LiftingProduct::with('lifting')
            ->whereHas('lifting', function ($query) use ($fromDate, $toDate) {
                $query->where('vouchar_date', '>=', $fromDate)
                    ->where('vouchar_date', '<=', $toDate);
            })
            ->where('product_id', $product_id)
            ->get();

        return $lifting->sum('qty');
    }

    public function liftingReturn()
    {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $liftingReturn = LiftingReturnProduct::with('LiftingReturn')
            ->whereHas('liftingReturn', function ($query) use ($fromDate, $toDate) {
                $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate);
            })
            ->where('product_id', $product_id)
            ->get();
        return $liftingReturn->sum('qty');
    }

    public function balance()
    {
        return $this->opening() + $this->lifting()-$this->liftingReturn() - $this->sales()+$this->salesReturn();
    }

    public function stockValue()
    {
        $product_id = $this->productId;
        $prouct = Product::findOrFail($product_id);
        $pp = $prouct->price;

        if (empty($prouct->price)) {
            $pp = 0;
        }

        return $this->balance() * $pp;
    }

    public function opening()
    {
        // Opening sale
        $product_id = $this->productId;

        $fromDate = $this->startDate;
        $openingStart = $this->openingStart;

        // get dealer sales
        $dealerSalesLineProduct = ProductIssueList::with('issue')
            ->whereHas('issue', function ($query) use ($openingStart, $fromDate) {
                $query->where('date', '>=', $openingStart)
                    ->where('date', '<', $fromDate);
            })
            ->where('product_id', $product_id)
            ->get();

        $dealerSalesLineProduct = $dealerSalesLineProduct->sum('qty');

        $retailSales = 0;

        $totalSales = $dealerSalesLineProduct + $retailSales;

        // Opening lifting start
        $lifting = LiftingProduct::with('lifting')
            ->whereHas('lifting', function ($query) use ($openingStart, $fromDate) {
                $query->where('vouchar_date', '>=', $openingStart)
                    ->where('vouchar_date', '<', $fromDate);
            })
            ->where('product_id', $product_id)
            ->get();

        $lifting = $lifting->sum('qty');

        $liftingReturn = LiftingReturnProduct::with('LiftingReturn')
            ->whereHas('liftingReturn', function ($query) use ($openingStart, $fromDate) {
                $query->where('date', '>=', $openingStart)
                    ->where('date', '<', $fromDate);
            })
            ->where('product_id', $product_id)
            ->get();

        $liftingReturn = $liftingReturn->sum('qty');

        $salesReturn = DB::table('tbl_sales_return')
                ->orWhere(function($query) use($openingStart, $fromDate){
                      $query->where('return_date', '>=', $openingStart)
                    ->where('return_date', '<', $fromDate); 
                })
                ->where('product_id', $product_id)
                ->get();
        $salesReturn = $salesReturn->sum('qty');

        $balance = $lifting - $liftingReturn - $totalSales + $salesReturn; 

        return $balance;
    }
}
