<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\Product;
use App\StoreSetup;
use App\TransferProduct;
use DB;
use PDF;
use MPDF;

class OutOfStockController extends Controller
{

    public function index(Request $request)
    {
        $title = "Closing Stock";
        $searchFormLink = "outOfStock.index";
        $printFormLink = "outOfStock.print";
        $print = $request->print;
        $store_id = $request->store_id;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];
        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::orderBy('name', 'asc')->get();
        $stores = StoreSetup::where('showroom_id', $this->showroomId)->orderBy('name', 'asc')->get();

        $storeIds = StoreSetup::where('showroom_id', $this->showroomId)->get()->pluck('id')->toArray();

        $stockOutReports = [];
        $transferRecProducts = [];
        if (!empty($print)) {

            if (in_array("All", $productCategory) || in_array("All", $product) && $productCategory == array() && $product == array()) {

                $stockOutReports = DB::table('view_out_of_stock')
                    ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', 'storeId', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty)+ SUM(productIssueReturnQty)) as remainingQty'))
                    ->where('showroomId', $this->showroomId);
                if ($store_id) {
                    $stockOutReports =  $stockOutReports->where('storeId', $store_id);
                }
                $stockOutReports = $stockOutReports->groupBy('categoryId', 'productId')
                    ->orderBy('productName', 'asc')
                    ->get();
            } else {

                $stockOutReports = DB::table('view_out_of_stock')
                    ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', 'storeId', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty) + SUM(productIssueReturnQty)) as remainingQty'))
                    ->orWhere(function ($query) use ($productCategory, $product) {
                        if (@$productCategory) {
                            $query->whereIn('categoryId', $productCategory);
                        }

                        if (@$product) {
                            $query->whereIn('productId', $product);
                        }
                    })
                    ->where('showroomId', $this->showroomId);
                if ($store_id) {
                    $stockOutReports =  $stockOutReports->where('storeId', $store_id);
                }
                $stockOutReports = $stockOutReports->groupBy('categoryId', 'productId')
                    ->orderBy('productName', 'asc')
                    ->get();

                $productIds = [];
                foreach ($stockOutReports as $stockOutReport) {
                    $productIds[] = $stockOutReport->productId;
                }
                $transferRecProducts = TransferProduct::with('transfer')
                    ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                        if ($store_id) {
                            $q->where('destination_id', $store_id);
                        } else {
                            $q->whereIn('destination_id', $storeIds);
                        }
                    })
                    ->whereNotNull('approve_by')
                    ->whereNotIn('product_id', $productIds);
                if ($product) {
                    $transferRecProducts =  $transferRecProducts->where('product_id', $product);
                }
                $transferRecProducts = $transferRecProducts->groupBy('product_id')
                    ->get();
            }
        }

        return view('admin.outOfStock.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'productCategory', 'product', 'print', 'categories', 'products', 'stockOutReports', 'stores', 'store_id', 'storeIds', 'transferRecProducts'));
    }

    public function print(Request $request)
    {
        $title = "Closing Stock";

        $print = $request->print;
        $store_id = $request->store_id;
        $productCategory = !empty($request->productCategory) ? $request->productCategory : [];
        $product = !empty($request->product) ? $request->product : [];
        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::orderBy('name', 'asc')->get();

        $storeIds = StoreSetup::where('showroom_id', $this->showroomId)->get()->pluck('id')->toArray();

        $transferRecProducts = [];

        if (!empty($print)) {

            if (in_array("All", $productCategory) || in_array("All", $product) && $productCategory == array() && $product == array()) {

                $stockOutReports = DB::table('view_out_of_stock')
                    ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', 'storeId', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty)+ SUM(productIssueReturnQty)) as remainingQty'))
                    ->where('showroomId', $this->showroomId);
                if ($store_id) {
                    $stockOutReports =  $stockOutReports->where('storeId', $store_id);
                }
                $stockOutReports = $stockOutReports->groupBy('categoryId', 'productId')
                    ->orderBy('productName', 'asc')
                    ->get();
            } else {

                $stockOutReports = DB::table('view_out_of_stock')
                    ->select('categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'reorderQty', 'storeId', DB::raw('(SUM(liftingQty) - SUM(liftingReturnQty) - SUM(invoiceQty) - SUM(productIssueQty) + SUM(productIssueReturnQty)) as remainingQty'))
                    ->orWhere(function ($query) use ($productCategory, $product) {
                        if (@$productCategory) {
                            $query->whereIn('categoryId', $productCategory);
                        }

                        if (@$product) {
                            $query->whereIn('productId', $product);
                        }
                    })
                    ->where('showroomId', $this->showroomId);
                if ($store_id) {
                    $stockOutReports =  $stockOutReports->where('storeId', $store_id);
                }
                $stockOutReports = $stockOutReports->groupBy('categoryId', 'productId')
                    ->orderBy('productName', 'asc')
                    ->get();

                $productIds = [];
                foreach ($stockOutReports as $stockOutReport) {
                    $productIds[] = $stockOutReport->productId;
                }
                $transferRecProducts = TransferProduct::with('transfer')
                    ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                        if ($store_id) {
                            $q->where('destination_id', $store_id);
                        } else {
                            $q->whereIn('destination_id', $storeIds);
                        }
                    })
                    ->whereNotNull('approve_by')
                    ->whereNotIn('product_id', $productIds);
                if ($product) {
                    $transferRecProducts =  $transferRecProducts->where('product_id', $product);
                }
                $transferRecProducts = $transferRecProducts->groupBy('product_id')
                    ->get();

                $productIds = [];
                foreach ($stockOutReports as $stockOutReport) {
                    $productIds[] = $stockOutReport->productId;
                }
                $transferRecProducts = TransferProduct::with('transfer')
                    ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                        if ($store_id) {
                            $q->where('destination_id', $store_id);
                        } else {
                            $q->whereIn('destination_id', $storeIds);
                        }
                    })
                    ->whereNotNull('approve_by')
                    ->whereNotIn('product_id', $productIds);
                if ($product) {
                    $transferRecProducts =  $transferRecProducts->where('product_id', $product);
                }
                $transferRecProducts = $transferRecProducts->groupBy('product_id')
                    ->get();
            }
        }

        $pdf = PDF::loadView('admin.outOfStock.print', ['title' => $title, 'storeIds' => $storeIds, 'store_id' => $store_id, 'stockOutReports' => $stockOutReports, 'transferRecProducts' => $transferRecProducts]);

        return $pdf->stream('closing_stock.pdf');
    }
}
