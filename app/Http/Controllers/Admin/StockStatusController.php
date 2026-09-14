<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\Product;
use App\VendorSetup;
use App\StoreSetup;
use App\Helper\Stock;
use App\CategorySetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockStatusController extends Controller {

    public function index(Request $request) {
        $title = "Stock Status";
        $searchFormLink = "stockStatus.index";
        $printFormLink = "stockStatus.print";

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $stockSearch = $request->stockSearch;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;

        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));

        $vendors = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $products = [];
        if ($type) {
            $products = Product::where('status', '1')
                    ->where('company_id', $this->company)
                    ->where('product_type', $type)
                    ->orderBy('name', 'asc')
                    ->get();
        }

        $categories = DB::table('tbl_categories as tab1')
                ->whereNull('parent')
                ->where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();


        $stores = StoreSetup::where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();

        $showroomId = $this->showroomId;


        $selectedProducts = Product::query()
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId);
                ->where('product_type', $type);
        if ($request->product) {
            $selectedProducts->whereIn('id', $request->product);
        }
        if ($category) {
            $selectedProducts->whereIn('category_id', $category);
        }
        $selectedProducts = $selectedProducts->get();


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

        $companyStores = array_merge([$this->company], $storeIds);

        $stockStatusReports = [];
        if ($type) {
            if ($type == 'warranty_product' || $type == 'spare_parts') {
                foreach ($selectedProducts as $selectedproduct) {
                    $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

                    $stockStatusReports[] = [
                        'product' => $selectedproduct,
                        'opening' => $stock->opening(),
                        'liftingQty' => $stock->lifting(),
                        'liftingReturnQty' => $stock->liftingReturn(),
                        'salesQty' => $stock->sales(),
                        'salesReturnQty' => $stock->salesReturn(),
                        'balanceQty' => $stock->balance(),
                    ];
                }
            } else {
                foreach ($selectedProducts as $selectedproduct) {

                    $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

                    $stockStatusReports[] = [
                        'product' => $selectedproduct,
                        'opening' => $stock->opening(),
                        'liftingQty' => $stock->lifting(),
                        'liftingReturnQty' => $stock->liftingReturn(),
                        'salesQty' => $stock->sales(),
                        'salesReturnQty' => $stock->salesReturn(),
                        'balanceQty' => $stock->balance(),
                    ];
                }
            }
        }



        return view('admin.stockStatus.index')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'vendors',
                                'categories',
                                'products',
                                'vendor',
                                'category',
                                'product',
                                'fromDate',
                                'toDate',
                                'stockStatusReports',
                                'lastDate',
                                'showroomId',
                                'stockSearch',
                                'productTypes',
                                'type',
                                'storeId',
                                'stores',
                                'categories'
        ));
    }

    public function print(Request $request) {
        $title = "Stock Status Report";

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $stockSearch = $request->stockSearch;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;



        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));


        $selectedProducts = Product::query()
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId);
                ->where('product_type', $type);
        if ($request->product) {
            $selectedProducts->whereIn('id', $request->product);
        }
        if ($category) {
            $selectedProducts->whereIn('category_id', $category);
        }
        $selectedProducts = $selectedProducts->get();


        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        }

        $companyStores = array_merge([$this->company], $storeIds);

        $stockStatusReports = [];

        if ($type) {
            if ($type == 'warranty_product' || $type == 'spare_parts') {
                foreach ($selectedProducts as $selectedproduct) {
                    $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

                    $stockStatusReports[] = [
                        'product' => $selectedproduct,
                        'opening' => $stock->opening(),
                        'liftingQty' => $stock->lifting(),
                        'liftingReturnQty' => $stock->liftingReturn(),
                        'salesQty' => $stock->sales(),
                        'salesReturnQty' => $stock->salesReturn(),
                        'balanceQty' => $stock->balance()
                    ];
                }
            } else {
                foreach ($selectedProducts as $selectedproduct) {

                    $stock = new Stock($fromDate, $toDate, $selectedproduct->id, $companyStores);

                    $stockStatusReports[] = [
                        'product' => $selectedproduct,
                        'opening' => $stock->opening(),
                        'liftingQty' => $stock->lifting(),
                        'liftingReturnQty' => $stock->liftingReturn(),
                        'salesQty' => $stock->sales(),
                        'salesReturnQty' => $stock->salesReturn(),
                        'balanceQty' => $stock->balance()
                    ];
                }
            }
        }

        $pdf = PDF::loadView('admin.stockStatus.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'stockStatusReports' => $stockStatusReports]);

        return $pdf->stream('stock_status_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
