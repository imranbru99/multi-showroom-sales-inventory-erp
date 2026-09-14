<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\CoaSetup;
use App\StaffSetup;
use App\DealerSetup;
use App\SalesReturn;
use App\ProductIssue;
use App\DealerCollection;
use App\ProductIssueList;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalesReturnHistoryController extends Controller {

    public function index(Request $request) {
        $title = "Sales Return";
        $searchFormLink = "salesReturnHistory.index";
        $printFormLink = "salesReturnHistory.print";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $print = $request->print;
        $search = $request->search;

        $dealers = DealerSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        if ($search == "search") {

            $allSalesReturn = DB::table('tbl_sales_return')
                    ->select('tbl_sales_return.*', 'tbl_dealers.name as dealerName', 'tbl_products.name as productName', 'tbl_products.model_no as modelName')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_sales_return.dealer_id')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_sales_return.product_id')
                    ->orWhere(function ($query) use ($fromDate, $toDate, $dealer) {

                        if ($fromDate != "1970-01-01") {
                            $query->whereBetween('tbl_sales_return.return_date', array($fromDate, $toDate));
                        }

                        if (!empty($dealer)) {

                            $query->whereIn('tbl_sales_return.dealer_id', $dealer);
                        }
                    })
                    ->where('tbl_sales_return.company_id', $this->company)
//                    ->where('tbl_sales_return.showroom_id', $this->showroomId)
                    ->orderBy('tbl_sales_return.id', 'desc')
                    ->get();
        } else {
            $allSalesReturn = SalesReturn::select('tbl_sales_return.*', 'tbl_dealers.name as dealerName', 'tbl_products.name as productName', 'tbl_products.model_no as modelName')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_sales_return.dealer_id')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_sales_return.product_id')
                    ->where('tbl_sales_return.company_id', $this->company)
//                    ->where('tbl_sales_return.showroom_id', $this->showroomId)
                    ->orderBy('tbl_sales_return.id', 'dsc')
                    ->get();
        }

        $allSalesReturn = $allSalesReturn->groupBy('issue_no');

        return view('admin.salesReturnHistory.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'allSalesReturn', 'dealers', 'dealer', 'fromDate', 'toDate'));
    }

    public function printChalan($productIssueId) {
        $title = "Print Challan";

        $returnInfo = SalesReturn::select(
                        'tbl_sales_return.*',
                        'tbl_dealers.name as dealerName',
                        'tbl_dealers.code as dealerCode',
                        'tbl_dealers.address as dealerAddress',
                        'tbl_dealers.mobile as dealerMobile',
                        // 'tbl_districts.name as districtsEnglishName',
                        // 'tbl_districts.bangla_name as districtsBanglaName',
                        // 'tbl_upazilas.name as upazilaEnglishName',
                        // 'tbl_upazilas.bangla_name as upazilaBanglaName',
                        'tbl_region.name as regionName',
                        'tbl_staffs.name as staffName'
                )
                ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_sales_return.dealer_id')
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_sales_return.sales_by')
                ->where('tbl_sales_return.issue_no', $productIssueId)
                ->first();

        $SalesReturn = SalesReturn::select('tbl_sales_return.*', 'tbl_products.name as productName', 'tbl_products.model_no as modelName')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_sales_return.product_id')
                ->where('tbl_sales_return.issue_no', $productIssueId)
                ->get();
        $groupByProductIds = $SalesReturn->groupBy('product_id');

        $pdf = PDF::loadView('admin.salesReturnHistory.printChalan', ['title' => $title, 'SalesReturn' => $SalesReturn, 'groupByProductIds' => $groupByProductIds, 'returnInfo' => $returnInfo]);

        return $pdf->stream('sales_return_' . $returnInfo->issue_no . '.pdf');
    }

    public function printInvoice($productIssueId) {
        $title = "Print Invoice";

        $returnInfo = SalesReturn::select(
                        'tbl_sales_return.*',
                        'tbl_dealers.name as dealerName',
                        'tbl_dealers.code as dealerCode',
                        'tbl_dealers.address as dealerAddress',
                        'tbl_dealers.mobile as dealerMobile',
                        'tbl_region.name as regionName',
                        'tbl_staffs.name as staffName'
                )
                ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_sales_return.dealer_id')
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_sales_return.sales_by')
                ->where('tbl_sales_return.issue_no', $productIssueId)
                ->first();

        $SalesReturn = SalesReturn::select('tbl_sales_return.*', 'tbl_products.name as productName', 'tbl_products.model_no as modelName')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_sales_return.product_id')
                ->where('tbl_sales_return.issue_no', $productIssueId)
                ->get();
        $groupByProductIds = $SalesReturn->groupBy('product_id');

        $pdf = PDF::loadView('admin.salesReturnHistory.printInvoice', ['title' => $title, 'SalesReturn' => $SalesReturn, 'groupByProductIds' => $groupByProductIds, 'returnInfo' => $returnInfo]);

        return $pdf->stream('sales_return_' . $returnInfo->issue_no . '.pdf');
    }

    public function dealerHistory(Request $request) {

        $title = "Dealer Sales Return History";
        $searchFormLink = "salesReturnHistory.history";
        $printFormLink = "salesReturnHistory.print";

//        dd($request->all());

        $dealer = $request->dealer;
        $category = $request->category;
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;
        $type = $request->productType;
        $product = !empty($request->product) ? $request->product : [];
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $regions = RegionSetup::where('company_id', $this->company)->get();

        $areas = [];
        $territories = [];
        if ($region) {
            $areas = AreaSetup::where('company_id', $this->company)
                    ->where('region_id', $region)
                    ->get();
        }
        if ($area) {
            $territories = TerritorySetup::where('company_id', $this->company)
                    ->where('area_id', $area)
                    ->get();
        }

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory) {
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $categories = DB::table('tbl_categories as tab1')
                ->select('tab1.*')
                ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
                ->whereNull('tab2.parent')
                ->where('tab1.company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $employees = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        // $productTypes = [
        //     'warranty_product' => 'Warranty Product',
        //     'consumer_product' => 'Consumer Product'
        // ];

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

        $products = Product::where('company_id', $this->company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();

        $productReturnHistories = array();

        $dealerIds = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory, $dealer) {
                    if ($dealer) {
                        $q->whereIn('id', $dealer);
                    }
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('id')
                ->toArray();

        if ($request->has('fromDate')) {

            $productReturnHistories = SalesReturn::with(['dealer', 'product.category'])
                    ->where('return_date', '>=', $fromDate)
                    ->where('return_date', '<=', $toDate)
                    ->where('company_id', $this->company)
                    ->where('product_type', $type)
//                ->where('showroom_id', $this->showroomId)
                    ->orderBy('return_date', 'desc');
            if ($product) {
                $productReturnHistories = $productReturnHistories->whereIn('product_id', $product);
            }

            if ($dealer) {
                $productReturnHistories = $productReturnHistories->whereIn('dealer_id', $dealerIds);
            }

            if ($category) {
                $productReturnHistories = $productReturnHistories->whereHas('product', function ($q) use ($category) {
                    $q->whereIn('category_id', $category);
                });
            }

            $productReturnHistories = $productReturnHistories->get();
        }

        return view('admin.salesReturnHistory.report')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'dealers',
                                'categories',
                                'dealer',
                                'category',
                                'fromDate',
                                'toDate',
                                'productReturnHistories',
                                'employees',
                                'region',
                                'regions',
                                'area',
                                'areas',
                                'territory',
                                'territories',
                                'productTypes',
                                'type',
                                'products',
                                'product'
        ));
    }

    public function print(Request $request) {

        $title = "Print Sales Return History";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $category = $request->category;
        $print = $request->print;
        $search = $request->search;
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;
        $type = $request->productType;
        $product = !empty($request->product) ? $request->product : [];

        $dealers = DealerSetup::where('status', '1')
                ->orderBy('name', 'asc')
                ->get();


        $dealerIds = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory, $dealer) {
                    if ($dealer) {
                        $q->whereIn('id', $dealer);
                    }
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('id')
                ->toArray();

        $productReturnHistories = SalesReturn::with(['dealer', 'product.category'])
                ->where('return_date', '>=', $fromDate)
                ->where('return_date', '<=', $toDate)
                ->where('company_id', $this->company)
                ->where('product_type', $type)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('return_date', 'desc');

        if ($product) {
            $productReturnHistories = $productReturnHistories->whereIn('product_id', $product);
        }

        if ($dealer) {
            $productReturnHistories = $productReturnHistories->whereIn('dealer_id', $dealer);
        }

        if ($category) {
            $productReturnHistories = $productReturnHistories->whereHas('product', function ($q) use ($category) {
                $q->whereIn('category_id', $category);
            });
        }

        $productReturnHistories = $productReturnHistories->get();


        $pdf = PDF::loadView('admin.salesReturnHistory.reportPrint', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'productReturnHistories' => $productReturnHistories,
                    'dealers' => $dealers,
                    'type' => $type
        ]);

        return $pdf->stream('sales_return_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
