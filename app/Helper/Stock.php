<?php

namespace App\Helper;

use App\Product;
use App\LiftingProduct;
use App\ProductIssueList;
use App\LiftingReturnProduct;
use App\SalesReturn;
use App\RetailSales;
use App\RetailSalesReturnProducts;
use App\TransferProduct;
use DB;

class Stock {

    public $openingStart = '0000-00-00';
    public $startDate = 0;
    public $endDate = 0;
    public $productId = 0;
    public $companyStores = [];

    public function __construct($startDate, $endDate, $productId, $companyStores) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->productId = $productId;
        $this->company = $companyStores[0];
        $this->store_id = array_splice($companyStores, 1);
    }

    public function sales() {
        $product_id = $this->productId;

        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $company = $this->company;

        // get dealer sales
        $dealerSalesLineProduct = ProductIssueList::with('issue')
                ->whereHas('issue', function ($query) use ($fromDate, $toDate, $company) {
                    $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->where('company_id', $company);
                })
                ->where('product_id', $product_id)
//                ->whereIn('store_id', $this->store_id)
                ->sum('qty');

        $retailSales = RetailSales::with('sale')
                ->whereHas('sale', function ($q) use ($fromDate, $toDate, $company) {
                    $q->where('company_id', $company)
                    ->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate);
                })
                ->whereIn('store_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');

        return $totalSales = $dealerSalesLineProduct + $retailSales;
    }

    public function salesReturn() {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $company = $this->company;

        $salesReturn = DB::table('tbl_sales_return')
                ->where(function($query) use($fromDate, $toDate) {
                    $query->where('return_date', '>=', $fromDate)
                    ->where('return_date', '<=', $toDate);
                })
//                ->whereIn('store_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');

        $retailReturns = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($fromDate, $toDate, $company) {
                    $q->where('company_id', $company)
                    ->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate);
                })
                ->whereIn('store_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');
        return $salesReturn + $retailReturns;
    }

    public function lifting() {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;

        // lifting start

        $lifting = LiftingProduct::with('lifting')
                ->whereHas('lifting', function ($query) use ($fromDate, $toDate) {
                    $query->where('vouchar_date', '>=', $fromDate)
                    ->where('vouchar_date', '<=', $toDate);
                })
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');

        return $lifting;
    }

    public function liftingReturn() {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $liftingReturn = LiftingReturnProduct::with('LiftingReturn')
                ->whereHas('liftingReturn', function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate);
                })
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $this->store_id)
                ->where('product_id', $product_id)
                ->get();
        return $liftingReturn->sum('qty');
    }

    public function transfer() {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $store_id = $this->store_id;
        $transfer = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($query) use ($fromDate, $toDate, $store_id) {
                    $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->whereIn('host_id', $store_id);
                })
                ->where('product_id', $product_id)
                ->sum('qty');

        return $transfer;
    }

    public function transferReceive() {
        $product_id = $this->productId;
        $fromDate = $this->startDate;
        $toDate = $this->endDate;
        $transferReceive = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->whereIn('destination_id', $this->store_id);
                })
                ->where('product_id', $product_id)
                ->sum('qty');
        return $transferReceive;
    }

    public function balance() {
        $balance = ($this->opening() + $this->lifting() + $this->transferReceive() + $this->salesReturn()) - ($this->liftingReturn() + $this->sales() + $this->transfer());
        return $balance;
    }

    public function stockValue() {
        $product_id = $this->productId;
        $prouct = Product::findOrFail($product_id);
        $pp = $prouct->price;

        if (empty($prouct->price)) {
            $pp = 0;
        }

        return $this->balance() * $pp;
    }

    public function opening() {
        // Opening sale
        $product_id = $this->productId;

        $fromDate = $this->startDate;
        $openingStart = $this->openingStart;
        $company = $this->company;
        $store_id = $this->store_id;

        // get dealer sales
        $dealerSalesLineProduct = ProductIssueList::with('issue')
                ->whereHas('issue', function ($query) use ($fromDate, $company) {
                    $query->where('date', '<', $fromDate);
                })
//                ->whereIn('store_id', $this->store_id)
                ->where('company_id', $company)
                ->where('product_id', $product_id)
                ->sum('qty');


        $retailSales = RetailSales::with('sale')
                ->whereHas('sale', function ($q) use ($fromDate, $company) {
                    $q->where('company_id', $company)
                    ->where('sale_date', '<', $fromDate);
                })
                ->whereIn('store_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');

        $totalSales = $dealerSalesLineProduct + $retailSales;

        // Opening lifting start
        $lifting = LiftingProduct::with('lifting')
                ->whereHas('lifting', function ($query) use ($fromDate) {
                    $query->where('vouchar_date', '<', $fromDate);
                })
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $this->store_id)
                ->where('company_id', $company)
                ->where('product_id', $product_id)
                ->sum('qty');

        $liftingReturn = LiftingReturnProduct::with('LiftingReturn')
                ->whereHas('liftingReturn', function ($query) use ($fromDate) {
                    $query->where('date', '<', $fromDate);
                })
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $this->store_id)
                ->where('company_id', $company)
                ->where('product_id', $product_id)
                ->sum('qty');

        $salesReturn = DB::table('tbl_sales_return')
                ->orWhere(function($query) use($fromDate) {
                    $query->where('return_date', '<', $fromDate);
                })
//                ->whereIn('store_id', $this->store_id)
                ->where('company_id', $company)
                ->where('product_id', $product_id)
                ->sum('qty');

        $retailReturns = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($fromDate, $company) {
                    $q->where('company_id', $company)
                    ->where('date', '<', $fromDate);
                })
                ->whereIn('store_id', $this->store_id)
                ->where('product_id', $product_id)
                ->sum('qty');

        $transferReceive = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($q) use ($company, $fromDate, $store_id) {
                    $q->whereIn('destination_id', $store_id)
                    ->where('company_id', $company)
                    ->where('date', '<', $fromDate);
                })
                ->where('product_id', $product_id)
                ->whereNotNull('approve_by')
                ->sum('qty');

        $transfer = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($q) use ($company, $fromDate, $store_id) {
                    $q->where('company_id', $company)
                    ->whereIn('host_id', $store_id)
                    ->where('date', '<', $fromDate);
                    ;
                })
                ->whereNotNull('approve_by')
                ->where('product_id', $product_id)
                ->sum('qty');

        $balance = $lifting + $salesReturn + $retailReturns - ($liftingReturn + $totalSales);

        return $balance;
    }

    //Dashboard Stock
    public static function StcockValue($company, $type = '') {
        $searchProducts = Product::with('category')
                ->where('company_id', $company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();

        $qty = 0;
        $stockValue = 0;
        foreach ($searchProducts as $searchProduct) {
            $liftingReturnSerials = LiftingReturnProduct::where('product_id', $searchProduct->id)
                    ->select(['serial_no'])
//                    ->where('store_or_showroom_type', 'store')
//                    ->whereIn('store_or_showroom_id', $store_id)
                    ->get()
                    ->pluck('serial_no')
                    ->toArray();

            $actualLiftingSerials = LiftingProduct::where('product_id', $searchProduct->id)
                    ->whereNotIn('serial_no', $liftingReturnSerials)
//                    ->where('store_or_showroom_type', 'store')
//                    ->whereIn('store_or_showroom_id', $store_id)
                    ->select(['serial_no'])
                    ->get()
                    ->pluck('serial_no')
                    ->toArray();

            $transfers = TransferProduct::with('transfer')
                    ->whereHas('transfer', function($q) use($searchProduct, $company) {
                        $q->where('product_id', $searchProduct->id)
                        ->where('company_id', $company);
//                        ->whereIn('host_id', $store_id);
                    })
                    ->get()
                    ->pluck('serial_no')
                    ->toArray();

            $transferRs = TransferProduct::with('transfer')
                    ->whereHas('transfer', function($q) use($searchProduct, $company) {
                        $q->where('product_id', $searchProduct->id)
                        ->where('company_id', $company);
//                        ->whereIn('destination_id', $store_id);
                    })
                    ->get()
                    ->pluck('serial_no')
                    ->toArray();

            $soldSerials = ProductIssueList::where('product_id', $searchProduct->id)
                    ->where('isReturn', '=', 0)
                    ->select(['serial_no'])
                    ->get()
                    ->pluck('serial_no')
                    ->toArray();

            $actualLiftingSerials = array_merge($actualLiftingSerials, $transferRs);
            $soldSerials = array_merge($soldSerials, $transfers);

            $actualLiftingSerials = array_unique($actualLiftingSerials);
            $soldSerials = array_unique($soldSerials);

            $notSoldSerials = array_diff($actualLiftingSerials, $soldSerials);

            if (!empty($notSoldSerials) && count($notSoldSerials) > 0) {
                $stockValue += LiftingProduct::whereIn('serial_no', $notSoldSerials)->sum('price');

                $qty += count($notSoldSerials);
            }
        }

        $info = [
            'stockValue' => $stockValue,
            'qty' => $qty
        ];

        return $info;
    }

    //Dashboard Stock
    public static function StockConsumerValue($company, $store_id, $type = '') {
        $searchProducts = Product::with('category')
                ->where('company_id', $company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();

        $info = [];
        $qty = 0;
        $stockValue = 0;
        foreach ($searchProducts as $searchProduct) {
            $lifting = LiftingProduct::where('product_id', $searchProduct->id)
                    ->where('company_id', $company)
//                    ->whereIn('store_or_showroom_id', $store_id)
//                    ->where('store_or_showroom_type', 'store')
                    ->sum('qty');

            $liftingR = LiftingReturnProduct::with('LiftingReturn')
                    ->whereHas('LiftingReturn', function ($q) use ($company) {
                        $q->where('company_id', $company);
                    })
                    ->where('product_id', $searchProduct->id)
//                    ->whereIn('store_or_showroom_id', $store_id)
//                    ->where('store_or_showroom_type', 'store')
                    ->sum('qty');


            $dealerSales = ProductIssueList::where('product_id', $searchProduct->id)
//                    ->whereIn('store_id', $store_id)
                    ->where('company_id', $company)
                    ->sum('qty');

            $dealerReturn = SalesReturn::where('product_id', $searchProduct->id)
                    ->where('company_id', $company)
                    ->sum('qty');

            $retailSales = RetailSales::with('sale')
                    ->whereHas('sale', function ($q) use ($company) {
                        $q->where('company_id', $company);
                    })
//                    ->whereIn('store_id', $store_id)
                    ->where('product_id', $searchProduct->id)
                    ->sum('qty');


            $retailReturns = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($q) use ($company) {
                        $q->where('company_id', $company);
                    })
//                    ->whereIn('store_id', $store_id)
                    ->where('product_id', $searchProduct->id)
                    ->sum('qty');


            $transferReceive = TransferProduct::with('transfer')
                    ->whereHas('transfer', function ($q) use ($company) {
                        $q->where('company_id', $company);
//                        ->whereIn('destination_id', $store_id);
                    })
                    ->where('product_id', $searchProduct->id)
                    ->whereNotNull('approve_by')
                    ->sum('qty');

            $transfer = TransferProduct::with('transfer')
                    ->whereHas('transfer', function ($q) use ($company) {
                        $q->where('company_id', $company);
//                        ->whereIn('host_id', $store_id);
                    })
                    ->whereNotNull('approve_by')
                    ->where('product_id', $searchProduct->id)
                    ->sum('qty');

            $inStock = ($lifting + $transferReceive + $dealerReturn + $retailReturns) - ($dealerSales + $retailSales + $transfer + $liftingR);


            if ($inStock > 0) {
                $stockValue += $searchProduct->price * $inStock;

                $qty += $inStock;
            }
        }

        $info = [
            'stockValue' => $stockValue,
            'qty' => $qty
        ];

        return $info;
    }

}
