<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use App\Product;
use App\GroupSetup;
use App\StaffSetup;
use App\ProductIssue;
use App\CategorySetup;
use App\CustomerRegistrationSetup;
use App\ShowroomSetup;
use App\ProductIssueList;
use Illuminate\Http\Request;
use App\Services\Product\Report;
use App\Http\Controllers\Controller;
use App\RetailSalesReturn;

class SalesHistoryController extends Controller
{

    public function index(Request $request)
    {
        $title = "Sales Report";
        $searchFormLink = "salesHistory.index";
        $printFormLink = "salesHistory.print";

        $showroomParam = $request->showRoom;
        $groupParam = $request->group;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $employee = $request->employee;
        $category = $request->category;
        $product = $request->product;
        $print = $request->print;
        $btnSummary = $request->btnSummary;
        $btnHistory = $request->btnHistory;
        $btnGroupSalesHistory = $request->btnGroupSalesHistory;
        $categories = CategorySetup::where('status', '1')
            ->where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::where('status', '1')
            ->where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->orderBy('name', 'asc')
            ->get();

        $showroomList = ShowroomSetup::where('status', 1)
            ->where('company_id', $this->company)
            ->orderBy('name', 'asc')
            ->get();

        $staffList = StaffSetup::where('status', 1)
            ->where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->orderBy('name', 'ASC')
            ->get();

        $groupList = GroupSetup::where('status', 1)
            ->where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->orderBy('name', 'ASC')
            ->get();

        if ($request->btnSummary == "Summary") {
            $salesSummary = Report::retailSummaryReport($fromDate, $toDate, $employee, $category, $product);
        } else {
            $salesSummary = "";
        }

        if ($request->btnHistory == "History") {
            $salesHistory = Report::RetailReport($fromDate, $toDate, $employee, $category, $product);
        } else {
            $salesHistory = "";
        }

        if ($request->btnGroupSalesHistory == "Group Sales History") {
            $groupSalesHistory = DB::table('view_sales_history')
                ->select('view_sales_history.*')
                ->orWhere(function ($query) use ($showroomParam, $groupParam, $fromDate, $toDate, $employee, $category, $product) {
                    if ($showroomParam) {
                        $query->whereIn('showroomId', $showroomParam);
                    }

                    if ($groupParam) {
                        $query->whereIn('groupId', $groupParam);
                    }

                    if (!empty($fromDate)) {
                        $query->whereBetween('purchaseDate', array($fromDate, $toDate));
                    }

                    // if ($employee) {
                    //     $query->whereIn('referenceId', $employee);
                    // }

                    if ($category) {
                        $query->whereIn('categoryId', $category);
                    }

                    if ($category) {
                        $query->orWhereIn('categoryParent', $category);
                    }

                    if ($product) {
                        $query->whereIn('productId', $product);
                    }
                })
                ->where('companyId', $this->company)
                //                    ->where('showroomId', $this->showroomId)
                ->orderBy('groupId', 'asc')
                ->groupBy('customerProductId')
                ->get();
        } else {
            $groupSalesHistory = "";
        }

        return view('admin.salesHistory.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'btnSummary', 'btnHistory', 'btnGroupSalesHistory', 'categories', 'products', 'showroomList', 'groupList', 'staffList', 'employee', 'category', 'product', 'showroomParam', 'groupParam', 'fromDate', 'toDate', 'salesHistory', 'salesSummary', 'groupSalesHistory'));
    }

    public function print(Request $request)
    {
        $title = "Print Sales History";

        $showroomParam = $request->showRoom;
        $groupParam = $request->group;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $employee = $request->employee;
        $category = $request->category;
        $product = $request->product;
        $btnPrintSummary = $request->btnPrintSummary;
        $btnPrintHistory = $request->btnPrintHistory;
        $btnPrintGroupSalesHistory = $request->btnPrintGroupSalesHistory;

        if ($request->btnPrintSummary == "Print Summary") {
            $salesSummary = Report::retailSummaryReport($fromDate, $toDate, $employee, $category, $product);
        } else {
            $salesSummary = "";
        }

        if ($request->btnPrintHistory == "Print History") {
            $salesHistory = Report::RetailReport($fromDate, $toDate, $employee, $category, $product);
        } else {
            $salesHistory = "";
        }

        if ($request->btnPrintGroupSalesHistory == "Print Group Sales History") {
            $groupSalesHistory = DB::table('view_sales_history')
                ->select('view_sales_history.*')
                ->orWhere(function ($query) use ($showroomParam, $groupParam, $fromDate, $toDate, $employee, $category, $product) {
                    if ($showroomParam) {
                        $query->whereIn('view_sales_history.showroomId', $showroomParam);
                    }

                    if ($groupParam) {
                        $query->whereIn('view_sales_history.groupId', $groupParam);
                    }

                    if (!empty($fromDate)) {
                        $query->whereBetween('view_sales_history.purchaseDate', array($fromDate, $toDate));
                    }

                    // if ($employee) {
                    //     $query->whereIn('view_sales_history.referenceId', $employee);
                    // }

                    if ($category) {
                        $query->whereIn('view_sales_history.categoryId', $category);
                    }

                    if ($category) {
                        $query->orWhereIn('view_sales_history.categoryParent', $category);
                    }

                    if ($product) {
                        $query->whereIn('view_sales_history.productId', $product);
                    }
                })
                ->where('companyId', $this->company)
                //                    ->where('showroomId', $this->showroomId)
                ->orderBy('view_sales_history.groupId', 'asc')
                ->groupBy('view_sales_history.customerProductId')
                ->get();
        } else {
            $groupSalesHistory = "";
        }

        $employee = StaffSetup::find($employee);

        $pdf = PDF::loadView('admin.salesHistory.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'btnPrintSummary' => $btnPrintSummary, 'btnPrintHistory' => $btnPrintHistory, 'btnPrintGroupSalesHistory' => $btnPrintGroupSalesHistory, 'salesHistory' => $salesHistory, 'salesSummary' => $salesSummary, 'groupSalesHistory' => $groupSalesHistory, 'employee' => $employee]);

        return $pdf->stream('sales_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    public function indexReturn(Request $request)
    {
        $title = "Sales Return History Report";
        $searchFormLink = "RetailsalesReturnHistory.index";
        $printFormLink = "RetailsalesReturnHistory.print";
        $print = $request->print;

        $customerId = $request->customer;
        $staffId = $request->staffId;

        $fromDate = date('Y-m-d', strtotime(now()));
        if ($request->fromDate) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        $toDate = date('Y-m-d', strtotime(now()));
        if ($request->fromDate) {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        $customers = CustomerRegistrationSetup::where('status', 1)->get();

        $staffs = StaffSetup::where('status', 1)->get();

        $salesHistory = [];

        if ($print) {
            $salesHistory = RetailSalesReturn::with(['products', 'customer'])
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate);

            if ($customerId) {
                $salesHistory = $salesHistory->where('customer_id', $customerId);
            }

            $salesHistory = $salesHistory->get();
        }
        // dd($request);

        $view = "search";

        if ($request->has('submit')) {
            if ($request->submit == 'print') {
                $view = 'print';
            }
        }


        if ($view == 'search') {

            return view('admin.salesHistory.return_index')->with(compact('salesHistory', 'title', 'searchFormLink', 'printFormLink', 'fromDate', 'toDate', 'customers', 'staffs', 'staffId', 'customerId'));
        } else {

            $pdf = PDF::loadView('admin.salesHistory.printReturn', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'salesHistory' => $salesHistory]);
            return $pdf->stream('sales_history_' . $fromDate . '_to_' . $toDate . '.pdf');
        }
    }

    public function salesDetails(Request $request)
    {
        $title = "Sales Details";
        $searchFormLink = "salesHistory.index";
        $printFormLink = "salesHistory.print";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $category = $request->category;
        $product = $request->product;
        $print = $request->print;
        $param = $request->by_param;
        $model = $request->model;
        $type = $request->productType;

        $categories = CategorySetup::where('status', '1')
            ->where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
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

        $products = [];
        if ($type) {
            $products = Product::where('company_id', $this->company)
                ->where('product_type', $type)
                ->orderBy('name', 'asc')
                ->get();
        }

        $staffs = StaffSetup::where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->get();
        $employee = $staffs->pluck('id')->toArray();

        $eWiseProductIssuelist = [];

        if ($request->has('submit')) {

            if ($request->staffs) {
                $employee = $request->staffs;
            }

            $eWiseProductIssuelist = [];

            foreach ($employee as $e) {

                $productIssuelist = ProductIssue::query()
                    ->where('company_id', $this->company)
                    ->where('product_type', $type);
                //                        ->where('showroom_id', $this->showroomId);

                if ($request->by_param == 'bySale') {

                    $productIssuelist = $productIssuelist->with(['products.product', 'collection'])
                        ->where('date', '>=', $fromDate)
                        ->where('date', '<=', $toDate);
                } else {

                    $productIssuelist = $productIssuelist->with(['products.product', 'collection'])
                        ->whereHas('products', function ($query) use ($fromDate, $toDate) {
                            $query->where('updated_at', '>=', $fromDate)
                                ->where('updated_at', '<=', $toDate)
                                ->where('collection', '>', 0);
                        });
                }

                $productIssuelist = $productIssuelist->where('sales_by', $e);

                if ($category) {
                    $productIssuelist = $productIssuelist->whereHas('products.product', function ($query) use ($category) {
                        $query->whereIn('category_id', $category);
                    });
                }

                if ($product) {
                    $productIssuelist = $productIssuelist->whereHas('products.product', function ($query) use ($product) {
                        $query->whereIn('id', $product);
                    });
                }

                $productIssuelist = $productIssuelist->orderBy('date', 'desc')->get();


                $lineArray = [
                    'eInfo' => StaffSetup::findOrFail($e),
                    'productIssuelist' => $productIssuelist,
                ];

                array_push($eWiseProductIssuelist, $lineArray);
            }
        }

        if (!$request->staffs) {
            $employee = [];
        }

        if ($request->submit == "Print") {
            $pdf = PDF::loadView('admin.sale_details.print', compact('title', 'param', 'searchFormLink', 'printFormLink', 'print', 'categories', 'products', 'staffs', 'employee', 'category', 'product', 'fromDate', 'toDate', 'eWiseProductIssuelist', 'type'));

            return $pdf->stream('sales_details_' . $fromDate . '_to_' . $toDate . '.pdf');
        }

        return view('admin.sale_details.index')->with(compact('title', 'param', 'searchFormLink', 'printFormLink', 'print', 'categories', 'products', 'staffs', 'employee', 'category', 'product', 'fromDate', 'toDate', 'eWiseProductIssuelist', 'productTypes', 'type'));
    }
}
