<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\Product;
use App\StoreSetup;
use DB;
use PDF;
use MPDF;

class OutOfStockController extends Controller {

    public function index(Request $request) {
        $title = "Closing Stock";
        $searchFormLink = "outOfStock.index";
        $printFormLink = "outOfStock.print";
        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];

        $categories = DB::table('tbl_categories as tab1')
                ->select('tab1.*')
                ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
                ->whereNull('tab2.parent')
                ->where('tab1.company_id', $this->company)
//                ->where('tab1.showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $products = [];
        if ($type) {
            $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('product_type', $type)
                    ->orderBy('name', 'asc')
                    ->get();
        }

        $stores = StoreSetup::where('company_id', $this->company)->get();

        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        }


        $productTypes = [
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ];


        $stockOutReports = DB::table('view_out_of_stock')
                ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty) + SUM(productIssueReturnQty)) as remainingQty'))
                ->orWhere(function($query) use($productCategory, $product) {
                    if (@$productCategory) {
                        $query->whereIn('categoryId', $productCategory);
                    }

                    if (@$product) {
                        $query->whereIn('productId', $product);
                    }
                })
                ->where('productType', $type)
                ->whereIn('storeId', $storeIds)
                ->where('type', 'store')
                ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                ->groupBy('categoryId', 'productId')
                ->orderBy('productName', 'asc')
                ->get();

        return view('admin.outOfStock.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'productCategory', 'product', 'print', 'categories', 'products', 'stockOutReports', 'productTypes', 'type', 'stores', 'storeId', 'storeIds'));
    }

    public function print(Request $request) {
        $title = "Closing Stock";

        $print = $request->print;
        $storeId = $request->store;
        $type = $request->productType;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];

        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = StoreSetup::where('company_id', $this->company)
                    ->get()
                    ->pluck('id')
                    ->toArray();
        }

        $categories = DB::table('tbl_categories as tab1')
                ->select('tab1.*')
                ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
                ->whereNull('tab2.parent')
                ->where('tab1.company_id', $this->company)
//                ->where('tab1.showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();


        $stockOutReports = DB::table('view_out_of_stock')
                ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty) + SUM(productIssueReturnQty)) as remainingQty'))
                ->orWhere(function($query) use($productCategory, $product) {
                    if (@$productCategory) {
                        $query->whereIn('categoryId', $productCategory);
                    }

                    if (@$product) {
                        $query->whereIn('productId', $product);
                    }
                })
                ->where('productType', $type)
                ->whereIn('storeId', $storeIds)
                ->where('type', 'store')
                ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                ->groupBy('categoryId', 'productId')
                ->orderBy('productName', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.outOfStock.print', [
                    'title' => $title,
                    'stockOutReports' => $stockOutReports,
                    'storeIds' => $storeIds,
                    'type' => $type
        ]);

        return $pdf->stream('out_of_stock.pdf');
    }

}
