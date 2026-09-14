<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\Product;
use App\ProductIssueList;
use App\SalesReturn;
use App\StoreSetup;
use App\TransferProduct;
use App\RetailSales;
use App\RetailSalesReturnProducts;
use App\LiftingProduct;
use App\LiftingReturnProduct;
use DB;
use PDF;
use MPDF;

class StockValuationController extends Controller {

    public function index(Request $request) {
        $title = "Stock Valuation";
        $searchFormLink = "stockValuation.index";
        $printFormLink = "stockValuation.print";
        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];

        $companyId = $this->company;

        $categories = DB::table('tbl_categories as tab1')
                ->whereNull('parent')
                ->where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();

        $products = Product::where('company_id', $this->company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();


        $stores = StoreSetup::where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();

        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        }

        $companyProductTypes = json_decode($this->companyInfo->product_types);

        $productTypes = collect([
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ]);

        $productTypes = $productTypes->filter(function($value, $key) use ($companyProductTypes){
            return in_array($key, $companyProductTypes);
        });

        if ($productCategory || $product) {
            if ($product) {
                $searchProducts = Product::with('category')
                        ->whereIn('id', $product)
                        ->where('company_id', $this->company)
                        ->where('product_type', $type)
                        ->orderBy('name', 'asc')
                        ->get();
            } elseif ($productCategory) {
                $searchProducts = Product::with('category')
                        ->whereHas('category', function ($q) use ($productCategory) {
                            $q->whereIn('id', $productCategory);
                        })
                        ->where('company_id', $this->company)
                        ->where('product_type', $type)
                        ->orderBy('name', 'asc')
                        ->get();
            }
        } else {
            $searchProducts = Product::with('category')
                    ->where('product_type', $type)
                    ->where('company_id', $this->company)
                    ->orderBy('name', 'asc')
                    ->get();
        }


        $stockOutReports = [];

        if ($print) {
            if ($type == 'warranty_product' || $type == 'spare_parts') {
                foreach ($searchProducts as $searchProduct) {
                    $stockOutReports[] = $this->stockSerialQty($searchProduct, $companyId, $storeIds);
                }
            } else {
                foreach ($searchProducts as $searchProduct) {
                    $stockOutReports[] = $this->stockQty($searchProduct, $companyId, $storeIds);
                }
            }
        }

        return view('admin.stockValuation.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'productCategory', 'product', 'print', 'categories', 'products', 'stockOutReports', 'stores', 'storeId', 'storeIds', 'productTypes', 'type'));
    }

    public function print(Request $request) {
        $title = "Stock Valuation Report";

        ini_set('memory_limit', '9216M');
        ini_set("pcre.backtrack_limit", "50000000");
        ini_set('max_execution_time', '10000');

        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];

        $companyId = $this->company;

        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        }

        if ($productCategory || $product) {
            if ($product) {
                $searchProducts = Product::with('category')
                        ->whereIn('id', $product)
                        ->where('company_id', $this->company)
                        ->where('product_type', $type)
                        ->orderBy('name', 'asc')
                        ->get();
            } elseif ($productCategory) {
                $searchProducts = Product::with('category')
                        ->whereHas('category', function ($q) use ($productCategory) {
                            $q->whereIn('id', $productCategory);
                        })
                        ->where('company_id', $this->company)
                        ->where('product_type', $type)
                        ->orderBy('name', 'asc')
                        ->get();
            }
        } else {
            $searchProducts = Product::with('category')
                    ->where('product_type', $type)
                    ->where('company_id', $this->company)
                    ->orderBy('name', 'asc')
                    ->get();
        }

        $stockOutReports = [];

        if ($print) {
            if ($type == 'warranty_product' || $type == 'spare_parts') {
                foreach ($searchProducts as $searchProduct) {
                    $stockOutReports[] = $this->stockSerialQty($searchProduct, $companyId, $storeIds);
                }
            } else {
                foreach ($searchProducts as $searchProduct) {
                    $stockOutReports[] = $this->stockQty($searchProduct, $companyId, $storeIds);
                }
            }
        }

        $pdf = PDF::loadView('admin.stockValuation.print', ['title' => $title, 'stockOutReports' => $stockOutReports]);
        return $pdf->stream('stock_valuation_report.pdf');
    }

    private function stockSerialQty($product, $companyId, $store_id) {

        $liftingReturnSerials = LiftingReturnProduct::where('product_id', $product->id)
                ->select(['serial_no'])
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $store_id)
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $actualLiftingSerials = LiftingProduct::where('product_id', $product->id)
                ->whereNotIn('serial_no', $liftingReturnSerials)
                ->where('store_or_showroom_type', 'store')
                ->whereIn('store_or_showroom_id', $store_id)
                ->select(['serial_no'])
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $transfers = TransferProduct::with('transfer')
                ->whereHas('transfer', function($q) use($product, $store_id) {
                    $q->where('product_id', $product->id)
                    ->whereIn('host_id', $store_id);
                })
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $transferR = TransferProduct::with('transfer')
                ->whereHas('transfer', function($q) use($product, $store_id) {
                    $q->where('product_id', $product->id)
                    ->whereIn('destination_id', $store_id);
                })
                ->whereIn('serial_no', $transfers)
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $transfers = array_diff($transfers, $transferR);


        $transferRs = TransferProduct::with('transfer')
                ->whereHas('transfer', function($q) use($product, $store_id) {
                    $q->where('product_id', $product->id)
                    ->whereIn('destination_id', $store_id);
                })
                ->whereNotIn('serial_no', $transfers)
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $soldSerials = ProductIssueList::where('product_id', $product->id)
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

        $stockOutReports = [
            'productId' => $product->id,
            'categoryName' => @$product->category->name,
            'productName' => $product->name,
            'productModel' => $product->model_no,
            'serialNo' => $notSoldSerials,
            'InStock' => count($notSoldSerials),
        ];

        return $stockOutReports;
    }

    private function stockQty($product, $companyId, $store_id) {

        $lifting = LiftingProduct::where('product_id', $product->id)
                ->where('company_id', $companyId)
                ->whereIn('store_or_showroom_id', $store_id)
                ->where('store_or_showroom_type', 'store')
                ->sum('qty');

        $liftingR = LiftingReturnProduct::with('LiftingReturn')
                ->whereHas('LiftingReturn', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->where('product_id', $product->id)
                ->whereIn('store_or_showroom_id', $store_id)
                ->where('store_or_showroom_type', 'store')
                ->sum('qty');


        $dealerSales = ProductIssueList::where('product_id', $product->id)
//                    ->whereIn('store_id', $store_id)
                ->where('company_id', $companyId)
                ->sum('qty');

        $dealerReturn = SalesReturn::where('product_id', $product->id)
                ->where('company_id', $companyId)
                ->sum('qty');

        $retailSales = RetailSales::with('sale')
                ->whereHas('sale', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->whereIn('store_id', $store_id)
                ->where('product_id', $product->id)
                ->sum('qty');


        $retailReturns = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->whereIn('store_id', $store_id)
                ->where('product_id', $product->id)
                ->sum('qty');


        $transferReceive = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($q) use ($companyId, $store_id) {
                    $q->where('company_id', $companyId)
                    ->whereIn('destination_id', $store_id);
                })
                ->where('product_id', $product->id)
                ->whereNotNull('approve_by')
                ->sum('qty');

        $transfer = TransferProduct::with('transfer')
                ->whereHas('transfer', function ($q) use ($companyId, $store_id) {
                    $q->where('company_id', $companyId)
                    ->whereIn('host_id', $store_id);
                })
                ->whereNotNull('approve_by')
                ->where('product_id', $product->id)
                ->sum('qty');

        $inStock = ($lifting + $transferReceive + $dealerReturn + $retailReturns) - ($dealerSales + $retailSales + $transfer + $liftingR);


        $stockOutReports = [
            'productId' => $product->id,
            'categoryName' => @$product->category->name,
            'productName' => $product->name,
            'productModel' => $product->model_no,
            'InStock' => $inStock,
        ];

        return $stockOutReports;
    }

}
