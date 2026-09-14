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

class LiftingRecordController extends Controller
{

    public function index(Request $request)
    {
        $title = "Lifting History";
        $searchFormLink = "liftingRecord.index";
        $printFormLink = "liftingRecord.print";

        if ($request->storeOrShowroom) {
            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];
        } else {
            $storeOrShowroomId = "";
            $storeOrShowroomType = "";
        }

        $store = $request->store;

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $type = $request->productType;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $btnSummary = $request->btnSummary;
        $btnRecord = $request->btnRecord;
        // dd($product);
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


        $companyProductTypes = json_decode($this->companyInfo->product_types);

        $productTypes = collect([
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ]);

        $productTypes = $productTypes->filter(function ($value, $key) use ($companyProductTypes) {
            return in_array($key, $companyProductTypes);
        });

        $stores = StoreSetup::where('company_id', $this->company)->get();


        if ($request->btnSummary == "Summary") {
            $liftingSummaries = DB::table('view_lifting_record')
                ->select(
                    'productName',
                    'productModelNo',
                    'vendorName',
                    'vendorId',
                    'categoryId',
                    'productId',
                    'categoryName',
                    'storeOrShowroomId',
                    DB::raw('SUM(productQty) as totalLifting'),
                    DB::raw('SUM(price) as totalLiftingPrice')
                )
                ->where(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('liftingDate', array($fromDate, $toDate));
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
        } else {
            $liftingSummaries = "";
        }

        if ($request->btnRecord == "Record") {
            $liftingRecords = DB::table('view_lifting_record')
                ->select('view_lifting_record.*')
                ->where(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('liftingDate', array($fromDate, $toDate));
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
                ->orderBy('vendorName', 'asc')
                ->get();
        } else {
            $liftingRecords = "";
        }

        return view('admin.liftingRecord.index')->with(
            compact(
                'title',
                'searchFormLink',
                'printFormLink',
                'print',
                'btnSummary',
                'btnRecord',
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
                'liftingRecords',
                'liftingSummaries',
                'productTypes',
                'stores',
                'type',
                'store'
            )
        );
    }

    public function print(Request $request)
    {
        // $title = "Print Lifting Record";

        $title = $request->btnPrintSummary ? "Print Lifting Summary" : "Print Lifting History";


        $storeOrShowroomId = $request->storeOrShowroomId;
        $storeOrShowroomType = $request->storeOrShowroomType;

        $vendor = $request->vendor;
        $category = $request->category;
        $product = $request->product;
        $type = $request->productType;

        $store = $request->store;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $btnPrintSummary = $request->btnPrintSummary;
        $btnPrintRecord = $request->btnPrintRecord;

        $vendorName = VendorSetup::where('id', $vendor)->first();


        if ($request->btnPrintSummary == "Print Summary") {
            $liftingSummaries = DB::table('view_lifting_record')
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
                        $query->whereBetween('liftingDate', array($fromDate, $toDate));
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
        } else {
            $liftingSummaries = "";
        }

        if ($request->btnPrintRecord == "Print Record") {
            $liftingRecords = DB::table('view_lifting_record')
                ->select('view_lifting_record.*')
                ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('liftingDate', array($fromDate, $toDate));
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
                ->orderBy('vendorName', 'asc')
                ->get();
        } else {
            $liftingRecords = "";
        }

        $pdf = PDF::loadView('admin.liftingRecord.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'btnPrintSummary' => $btnPrintSummary, 'btnPrintRecord' => $btnPrintRecord, 'vendorName' => $vendorName, 'liftingRecords' => $liftingRecords, 'liftingSummaries' => $liftingSummaries]);


        $liftingSummaryFileName = "lifting_summary_". $fromDate . "_to_" . $toDate .".pdf";
        $liftingHistoryFileName = "lifting_history_". $fromDate . "_to_" . $toDate .".pdf";

        $file_name = $request->btnPrintSummary == "Print Summary" ? $liftingSummaryFileName : $liftingHistoryFileName;

        return $pdf->stream($file_name);

        // return $pdf->stream('lifting_record_' . $fromDate . '_to_' . $toDate . '.pdf');
    }
}
