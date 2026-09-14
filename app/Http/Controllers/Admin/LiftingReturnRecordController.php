<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorSetup;
use App\CategorySetup;
use App\Product;
use App\StoreSetup;
use DB;
use PDF;
use MPDF;

class LiftingReturnRecordController extends Controller {

    public function index(Request $request) {
        // dd($request->all());
        $title = "Lifting Return History";
        $searchFormLink = "liftingReturnRecord.index";
        $printFormLink = "liftingReturnRecord.print";

        if ($request->storeOrShowroom) {
            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];
        } else {
            $storeOrShowroomId = "";
            $storeOrShowroomType = "";
        }

        $btnSummary = $request->btnSummary;
        $btnRecord = $request->btnRecord;

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $type = $request->productType;
        $store = $request->store;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

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
            $products = DB::table('tbl_products')
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('product_type', $type)
                    ->orderBy('name', 'asc')
                    ->get();
        }

        $storesAndShowrooms = DB::table('view_store_and_showroom')
//                ->where('type', 'showroom')
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
//                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        $stores = StoreSetup::where('company_id', $this->company)->get();


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

        $liftingReturnSummaries = [];
        $liftingReturnRecords = [];
        if ($request->btnSummary == "Summary") {
            $liftingReturnSummaries = DB::table('view_lifting_return_record')
                    ->select(
                            'productName',
                            'productModelNo',
                            'vendorName',
                            'vendorId',
                            'categoryId',
                            'productId',
                            'categoryName',
                            DB::raw('SUM(productQty) as totalLifting'),
                            DB::raw('SUM(price) as totalLiftingPrice')
                    )
                    ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('vendorId', $vendor);
                        }

                        if ($category) {
                            $query->whereIn('categoryId', $category);
                        }

                        if ($type) {
                            $query->where('productType', $type);
                        }

                        if ($product) {
                            $query->whereIn('productId', $product);
                        }

                        if ($store) {
                            $query->where('storeOrShowroomType', 'store')
                            ->where('storeOrShowroomId', $store);
                        }
                    })
                    ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                    ->groupBy('vendorId')
//                    ->groupBy('productId')
//                    ->orderBy('productName', 'asc')
                    ->get();
        }


        if ($request->btnRecord == "Record") {
            $liftingReturnRecords = DB::table('view_lifting_return_record')
                    ->select('view_lifting_return_record.*')
                    ->orWhere(function($query) use($fromDate, $toDate, $vendor, $category, $product, $type, $storeOrShowroomType, $storeOrShowroomId) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('vendorId', $vendor);
                        }

                        if ($category) {
                            $query->whereIn('categoryId', $category);
                        }

                        if ($category) {
                            $query->orWhereIn('parentId', $category);
                        }

                        if ($product) {
                            $query->whereIn('productId', $product);
                        }
                    })
                    ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
                    ->get();
        }
        // dd($liftingReturnRecords);


        return view('admin.liftingReturnRecord.index')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'vendors',
                                'categories',
                                'products',
                                'storesAndShowrooms',
                                'vendor',
                                'category',
                                'product',
                                'type',
                                'storeOrShowroomId',
                                'storeOrShowroomType',
                                'fromDate',
                                'toDate',
                                'liftingReturnRecords',
                                'productTypes',
                                'stores',
                                'type',
                                'store',
                                'btnSummary',
                                'btnRecord',
                                'liftingReturnSummaries'
        ));
    }

    public function print(Request $request) {

        // $title = "Print Lifting Return History";

        $title = $request->btnPrintSummary ? "Print Lifting Return Summary" : "Print Lifting Return History";

        $storeOrShowroomType = $request->storeOrShowroomType;
        $storeOrShowroomId = $request->storeOrShowroomId;

        if ($request->storeOrShowroom) {
            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];
        } else {
            $storeOrShowroomId = "";
            $storeOrShowroomType = "";
        }

        $btnPrintSummary = $request->btnPrintSummary;
        $btnPrintRecord = $request->btnPrintRecord;

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $type = $request->productType;
        $store = $request->store;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;


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

        $products = DB::table('tbl_products')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $storesAndShowrooms = DB::table('view_store_and_showroom')
//                ->where('type', 'showroom')
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
//                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        $vendorName = VendorSetup::where('id', $vendor)->first();

        $liftingReturnSummaries = [];
        $liftingReturnRecords = [];
        if ($request->btnPrintSummary == "Print Summary") {
            $liftingReturnSummaries = DB::table('view_lifting_return_record')
                    ->select(
                            'productName',
                            'productModelNo',
                            'vendorName',
                            'vendorId',
                            'categoryId',
                            'productId',
                            'categoryName',
                            DB::raw('SUM(productQty) as totalLifting'),
                            DB::raw('SUM(price) as totalLiftingPrice')
                    )
                    ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('vendorId', $vendor);
                        }

                        if ($category) {
                            $query->whereIn('categoryId', $category);
                        }

                        if ($type) {
                            $query->where('productType', $type);
                        }

                        if ($product) {
                            $query->whereIn('productId', $product);
                        }

                        if ($store) {
                            $query->where('storeOrShowroomType', 'store')
                            ->where('storeOrShowroomId', $store);
                        }
                    })
                    ->where('companyId', $this->company)
//                    ->where('showroomId', $this->showroomId)
                    ->groupBy('vendorId')
//                    ->groupBy('productId')
//                    ->orderBy('productName', 'asc')
                    ->get();
        }



        $liftingReturnRecords = DB::table('view_lifting_return_record')
                ->select('view_lifting_return_record.*')
                ->orWhere(function($query) use($fromDate, $toDate, $vendor, $category, $product, $type, $storeOrShowroomType, $storeOrShowroomId) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                    }

                    if ($vendor) {
                        $query->whereIn('vendorId', $vendor);
                    }

                    if ($category) {
                        $query->whereIn('categoryId', $category);
                    }

                    if ($category) {
                        $query->orWhereIn('parentId', $category);
                    }

                    if ($product) {
                        $query->whereIn('productId', $product);
                    }

                    if ($type) {
                        $query->where('storeOrShowroomType', $type);
                    }

                    if ($storeOrShowroomType && $storeOrShowroomId) {
                        $query->where('storeOrShowroomType', $storeOrShowroomType)
                        ->where('storeOrShowroomId', $storeOrShowroomId);
                    }
                })
                ->where('companyId', $this->company)
                ->where('showroomId', $this->showroomId)
                ->get();


        $pdf = PDF::loadView('admin.liftingReturnRecord.print', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'liftingReturnRecords' => $liftingReturnRecords,
                    'liftingReturnSummaries' => $liftingReturnSummaries,
                    'btnPrintSummary' => $btnPrintSummary,
                    'btnPrintRecord' => $btnPrintRecord,
                    'vendorName' => $vendorName,
        ]);


        $liftingReturnSummaryFileName = "lifting_return_summary_". $fromDate . "_to_" . $toDate .".pdf";
        $liftingReturnHistoryFileName = "lifting_return_history_". $fromDate . "_to_" . $toDate .".pdf";

        $file_name = $request->btnPrintSummary == "Print Summary" ? $liftingReturnSummaryFileName : $liftingReturnHistoryFileName;

        return $pdf->stream($file_name);
    }

}
