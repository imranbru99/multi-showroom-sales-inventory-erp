<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\StaffSetup;
use App\DealerSetup;
use App\CategorySetup;
use App\EmployeeSetup;
use App\ProductIssueList;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealerSalesContributionController extends Controller {

    public function index(Request $request) {
        $title = "Dealer Sales Contribution";
        $searchFormLink = "dealerSalesContribution.index";
        $printFormLink = "dealerSalesContribution.print";
        $criteria = $request->criteria;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $totalSalesContributions = [];
        $salesContributions = [];
        $data = [];

        if ($print) {

            $criteria = $request->criteria;

            if ($request->criteria == "dealer") {

                $dealers = DealerSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                                ->where('status', 1)->get();

                $data = [];


                foreach ($dealers as $dealer) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $dealer) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $dealer) {
                                $q->where('dealer_id', $dealer->id)
                                ->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['dealer_name'] = $dealer->name;
                    $ld['dealer_qty'] = $salesContributions->sum('qty');
                    $ld['dealer_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['dealer_value'] = $salesContributions->sum('amount');
                    $ld['dealer_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            if ($request->criteria == "product") {
                $totalSalesContributions = ProductIssueList::with(['issue'])
                        ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('date', array($fromDate, $toDate));
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $salesContributions = ProductIssueList::with(['issue', 'product'])
                        ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('date', array($fromDate, $toDate));
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $salesContributions = $salesContributions->groupBy('product_id');
            }

            if ($request->criteria == "category") {

                $categories = CategorySetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];

                foreach ($categories as $category) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('product', function ($q) use ($category) {
                                $q->where('category_id', $category->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('product', function ($q) use ($category) {
                                $q->where('category_id', $category->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['category_name'] = $category->name;
                    $ld['category_qty'] = $salesContributions->sum('qty');
                    $ld['category_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['category_value'] = $salesContributions->sum('amount');
                    $ld['category_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            if ($request->criteria == "employee") {

                $employees = StaffSetup::where('status', 1)->get();

                $data = [];

                foreach ($employees as $employee) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $employee) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $employee) {
                                $q->whereBetween('date', array($fromDate, $toDate))
                                ->where('sales_by', $employee->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['employee_name'] = $employee->name;
                    $ld['product_qty'] = $salesContributions->sum('qty');
                    $ld['product_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['product_value'] = $salesContributions->sum('amount');
                    $ld['product_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }

                // dd($data);
            }

            if ($request->criteria == "dealer_type") {

                $types = ['Internal', 'External'];

                $data = [];

                foreach ($types as $type) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $type) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['type'] = $type;
                    $ld['product_qty'] = $salesContributions->sum('qty');
                    $ld['product_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['product_value'] = $salesContributions->sum('amount');
                    $ld['product_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }


            //Region
            if ($request->criteria == "region") {

                $regions = RegionSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($regions as $region) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($region) {
                                $q->where('region_id', $region->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['region_name'] = $region->name;
                    $ld['region_qty'] = $salesContributions->sum('qty');
                    $ld['region_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['region_value'] = $salesContributions->sum('amount');
                    $ld['region_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }


            //Area Name
            if ($request->criteria == "area") {

                $areas = AreaSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($areas as $area) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($area) {
                                $q->where('area_id', $area->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['area_name'] = $area->name;
                    $ld['area_qty'] = $salesContributions->sum('qty');
                    $ld['area_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['area_value'] = $salesContributions->sum('amount');
                    $ld['area_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            //Territory Name
            if ($request->criteria == "territory") {

                $territories = TerritorySetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($territories as $territory) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($territory) {
                                $q->where('territory_id', $territory->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['territory_name'] = $territory->name;
                    $ld['territory_qty'] = $salesContributions->sum('qty');
                    $ld['territory_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['territory_value'] = $salesContributions->sum('amount');
                    $ld['territory_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }
        }



        return view('admin.dealerSalesContribution.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'salesContributions', 'criteria', 'totalSalesContributions', 'data'));
    }

    public function print(Request $request) {
        $title = "Print Dealer Sales Contribution";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $totalSalesContributions = [];
        $salesContributions = [];
        $data = [];

        if ($print) {

            $criteria = $request->criteria;

            if ($request->criteria == "dealer") {

                $dealers = DealerSetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($dealers as $dealer) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $dealer) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $dealer) {
                                $q->where('dealer_id', $dealer->id)
                                ->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['dealer_name'] = $dealer->name;
                    $ld['dealer_qty'] = $salesContributions->sum('qty');
                    $ld['dealer_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['dealer_value'] = $salesContributions->sum('amount');
                    $ld['dealer_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            if ($request->criteria == "product") {
                $totalSalesContributions = ProductIssueList::with(['issue'])
                        ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('date', array($fromDate, $toDate));
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $salesContributions = ProductIssueList::with(['issue', 'product'])
                        ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('date', array($fromDate, $toDate));
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $salesContributions = $salesContributions->groupBy('product_id');
            }

            if ($request->criteria == "category") {

                $categories = CategorySetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];

                foreach ($categories as $category) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('product', function ($q) use ($category) {
                                $q->where('category_id', $category->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('product', function ($q) use ($category) {
                                $q->where('category_id', $category->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['category_name'] = $category->name;
                    $ld['category_qty'] = $salesContributions->sum('qty');
                    $ld['category_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['category_value'] = $salesContributions->sum('amount');
                    $ld['category_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            if ($request->criteria == "employee") {

                $employees = StaffSetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];

                foreach ($employees as $employee) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $employee) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $employee) {
                                $q->whereBetween('date', array($fromDate, $toDate))
                                ->where('sales_by', $employee->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['employee_name'] = $employee->name;
                    $ld['product_qty'] = $salesContributions->sum('qty');
                    $ld['product_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['product_value'] = $salesContributions->sum('amount');
                    $ld['product_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }

                // dd($data);
            }

            if ($request->criteria == "dealer_type") {

                $types = ['Internal', 'External'];

                $data = [];

                foreach ($types as $type) {
                    $ld = [];


                    $totalSalesContributions = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $type) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with(['issue.dealer', 'product'])
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($type) {
                                $q->where('type', $type);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;


                    $ld['type'] = $type;
                    $ld['product_qty'] = $salesContributions->sum('qty');
                    $ld['product_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['product_value'] = $salesContributions->sum('amount');
                    $ld['product_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            //Region
            if ($request->criteria == "region") {

                $regions = RegionSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($regions as $region) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($region) {
                                $q->where('region_id', $region->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['region_name'] = $region->name;
                    $ld['region_qty'] = $salesContributions->sum('qty');
                    $ld['region_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['region_value'] = $salesContributions->sum('amount');
                    $ld['region_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }


            //Area Name
            if ($request->criteria == "area") {

                $areas = AreaSetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($areas as $area) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($area) {
                                $q->where('area_id', $area->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['area_name'] = $area->name;
                    $ld['area_qty'] = $salesContributions->sum('qty');
                    $ld['area_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['area_value'] = $salesContributions->sum('amount');
                    $ld['area_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }

            //Territory Name
            if ($request->criteria == "territory") {

                $territories = TerritorySetup::where('company_id', $this->company)
//                                ->where('showroom_id', $this->showroomId)
                        ->where('status', 1)
                        ->get();

                $data = [];


                foreach ($territories as $territory) {
                    $ld = [];

                    $totalSalesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();

                    $salesContributions = ProductIssueList::with('issue', 'product', 'issue.dealer')
                            ->whereHas('issue', function ($q) use ($fromDate, $toDate) {
                                $q->whereBetween('date', array($fromDate, $toDate));
                            })
                            ->whereHas('issue.dealer', function ($q) use ($territory) {
                                $q->where('territory_id', $territory->id);
                            })
                            ->where('company_id', $this->company)
//                            ->where('showroom_id', $this->showroomId)
                            ->get();


                    $totalQty = $totalSalesContributions->sum('qty') > 0 ? $totalSalesContributions->sum('qty') : 1;
                    $totalValue = $totalSalesContributions->sum('amount') > 0 ? $totalSalesContributions->sum('amount') : 1;

                    $ld['territory_name'] = $territory->name;
                    $ld['territory_qty'] = $salesContributions->sum('qty');
                    $ld['territory_qty_p'] = round(($salesContributions->sum('qty') * 100) / $totalQty, 2);
                    $ld['territory_value'] = $salesContributions->sum('amount');
                    $ld['territory_value_p'] = round(($salesContributions->sum('amount') * 100) / $totalValue, 2);

                    array_push($data, $ld);
                }
            }
        }

        $pdf = PDF::loadView('admin.dealerSalesContribution.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'salesContributions' => $salesContributions, 'criteria' => $criteria, 'totalSalesContributions' => $totalSalesContributions, 'data' => $data]);

        return $pdf->stream('dealer_sales_contribution_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
