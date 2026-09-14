<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use App\CategorySetup;
use App\EmployeeSetup;
use App\StaffSetup;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use App\Product;
use DB;
use MPDF;
use PDF;

class ProductIssueHistoryController extends Controller {

    public function getDealerByType(Request $request) {
        $type = $request->dealer_type;

        if (!$type) {
            return DealerSetup::where('company_id', $this->company)->all();
        }

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('type', $type)
                ->get();

        return $dealers;
    }

    public function index(Request $request) {
        $title = "Dealer Sales History";
        $searchFormLink = "productIssueHistory.index";
        $printFormLink = "productIssueHistory.print";

        $dealer = $request->dealer;
        $category = $request->category;
        $saleBy = $request->sale_by;
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


        $categories = DB::table('tbl_categories as tab1')
                ->select('tab1.*')
                ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
                ->whereNull('tab2.parent')
                ->where('tab1.company_id', $this->company)
//                ->where('tab1.showroom_id', $this->showroomId)
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

        $productTypes = $productTypes->filter(function ($value, $key) use ($companyProductTypes) {
            return in_array($key, $companyProductTypes);
        });

        $products = Product::where('company_id', $this->company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();

        $productIssueHistories = array();

        if (!$dealer) {
            if ($request->dealer_type) {
                $dealerIds = DealerSetup::where('type', $request->dealer_type)
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->select(['id'])
                        ->get()
                        ->pluck('id');
            } else {
                $dealerIds = $dealerIds;
            }
        }

//        if ($statement) {
            $productIssueHistories = DB::table('view_product_issue_history')
                    ->select('date', 'dealerId', 'dealerName', 'categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'productType', DB::raw('GROUP_CONCAT(productSerialNO) as totalProductSerialNO'), DB::raw('SUM(issueQty) as totalIssueQty'), DB::raw('SUM(issueAmount) as totalIssueAmount'))
                    ->orWhere(function ($query) use ($fromDate, $toDate, $dealerIds, $category, $saleBy, $product, $type) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('date', array($fromDate, $toDate));
                        }

                        if ($product) {
                            $query->whereIn('productId', $product);
                        }

                        if ($category) {
                            $query->whereIn('categoryId', $category);
                        }

                        if ($type) {
                            $query->where('productType', $type);
                        }

                        if ($saleBy) {
                            $query->whereIn('saleBy', $saleBy);
                        }
                    })
                    ->whereIn('dealerId', $dealerIds)
                    ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
                    ->orderBy('date', 'desc')
                    ->groupBy('productId')
                    ->groupBy('modelNo')
                    ->groupBy('dealerId')
                    ->groupBy('date')
                    ->get();
//        } elseif ($summary) {
//            $productIssueSummary = DB::table('view_product_issue_history')
//                    ->select('date', 'dealerId', 'dealerName', 'categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'productType', DB::raw('GROUP_CONCAT(productSerialNO) as totalProductSerialNO'), DB::raw('SUM(issueQty) as totalIssueQty'), DB::raw('SUM(issueAmount) as totalIssueAmount'))
//                    ->orWhere(function ($query) use ($fromDate, $toDate, $dealerIds, $category, $saleBy, $product, $type) {
//                        if (!empty($fromDate)) {
//                            $query->whereBetween('date', array($fromDate, $toDate));
//                        }
//
//                        if ($product) {
//                            $query->whereIn('productId', $product);
//                        }
//
//                        if ($category) {
//                            $query->whereIn('categoryId', $category);
//                        }
//
//                        if ($type) {
//                            $query->where('productType', $type);
//                        }
//
//                        if ($saleBy) {
//                            $query->whereIn('saleBy', $saleBy);
//                        }
//                    })
//                    ->whereIn('dealerId', $dealerIds)
//                    ->where('companyId', $this->company)
////                ->where('showroomId', $this->showroomId)
//                    ->orderBy('date', 'desc')
//                    ->groupBy('productId')
//                    ->groupBy('modelNo')
//                    ->groupBy('dealerId')
//                    ->groupBy('date')
//                    ->get();
//        }

        return view('admin.productIssueHistory.index')->with(compact(
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
                                'productIssueHistories',
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
        $title = "Dealer Sales History";

        $dealer = $request->dealer;
        $category = $request->category;
        $saleBy = $request->sale_by;
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;
        $type = $request->productType;
        $product = !empty($request->product) ? $request->product : [];
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;


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

        $productIssueHistories = array();

        if (!$dealer) {
            if ($request->dealer_type) {
                $dealerIds = DealerSetup::where('type', $request->dealer_type)
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->select(['id'])
                        ->get()
                        ->pluck('id');
            } else {
                $dealerIds = $dealerIds;
            }
        }

        $productIssueHistories = DB::table('view_product_issue_history')
                ->select('date', 'dealerId', 'dealerName', 'categoryId', 'categoryName', 'productId', 'productName', 'modelNo', 'productType', DB::raw('GROUP_CONCAT(productSerialNO) as totalProductSerialNO'), DB::raw('SUM(issueQty) as totalIssueQty'), DB::raw('SUM(issueAmount) as totalIssueAmount'))
                ->orWhere(function ($query) use ($fromDate, $toDate, $dealerIds, $category, $saleBy, $product, $type) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($product) {
                        $query->whereIn('productId', $product);
                    }

                    if ($category) {
                        $query->whereIn('categoryId', $category);
                    }

                    if ($type) {
                        $query->where('productType', $type);
                    }

                    if ($saleBy) {
                        $query->whereIn('saleBy', $saleBy);
                    }
                })
                ->whereIn('dealerId', $dealerIds)
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
                ->orderBy('date', 'desc')
                ->groupBy('productId')
                ->groupBy('modelNo')
                ->groupBy('dealerId')
                ->groupBy('date')
                ->get();

        $pdf = PDF::loadView('admin.productIssueHistory.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'productIssueHistories' => $productIssueHistories]);

        return $pdf->stream('product_issue_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    public function dealerArea(Request $request) {
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;


        $dealers = DealerSetup::where('company_id', $this->company)
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

        $info = [
            'dealers' => $dealers,
            'areas' => $areas,
            'territories' => $territories,
        ];

        return $info;
    }

}
