<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\CategorySetup;
use App\Product;
use App\ProductIssueList;

class ProductWiseProfitController extends Controller
{

    public function index(Request $request)
    {
        $title = "Product Wise Profit";
        $searchFormLink = "productWiseProfit.index";
        $printFormLink = "productWiseProfit.print";
        $print = $request->print;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $type = $request->productType;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $productTypes = [
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product'
        ];

        $data = [];

        $categories = CategorySetup::where('company_id', $this->company)->get();

        $products = [];
        if ($type) {
            $products = Product::where('company_id', $this->company)
                ->where('product_type', $type)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();
        }


        $sales = ProductIssueList::select(
            'tbl_products.name as productName',
            'tbl_categories.name as productCategory',
            'tbl_products.price as consumerPrice',
            'tbl_product_issue_lists.model_no as productModel',
            DB::raw('count(tbl_product_issue_lists.id) as qty'),
            DB::raw('sum(tbl_product_issue_lists.amount) as salePrice'),
            DB::raw('sum(tbl_lifting_products.price) as liftingPrice')
        )
            ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
            ->leftjoin('tbl_categories', 'tbl_products.category_id', '=', 'tbl_categories.id')
            ->leftjoin('tbl_product_issue', 'tbl_product_issue.id', '=', 'tbl_product_issue_lists.issue_id')
            ->leftjoin('tbl_lifting_products', 'tbl_lifting_products.serial_no', '=', 'tbl_product_issue_lists.serial_no');

        if ($category_id) {
            $sales = $sales->whereIn('tbl_products.category_id', $category_id);
        }

        if ($product_id) {
            $sales = $sales->whereIn('tbl_products.id', $product_id);
        }
        $sales = $sales->where('tbl_product_issue_lists.company_id', $this->company)
            ->where('tbl_product_issue.product_type', $type)
            ->where('tbl_product_issue.date', '>=', $fromDate)
            ->where('tbl_product_issue.date', '<=', $toDate)
            ->groupBy('tbl_product_issue_lists.product_id')
            ->get();


        return view('admin.productWiseProfit.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'products', 'categories', 'product_id', 'category_id', 'sales', 'productTypes', 'type'));
    }

    public function print(Request $request)
    {
        $title = "Product Wise Profit";

        $print = $request->print;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $type = $request->productType;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $sales = ProductIssueList::select(
            'tbl_products.name as productName',
            'tbl_categories.name as productCategory',
            'tbl_products.price as consumerPrice',
            'tbl_product_issue_lists.model_no as productModel',
            DB::raw('count(tbl_product_issue_lists.id) as qty'),
            DB::raw('sum(tbl_product_issue_lists.amount) as salePrice'),
            DB::raw('sum(tbl_lifting_products.price) as liftingPrice')
        )
            ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
            ->leftjoin('tbl_categories', 'tbl_products.category_id', '=', 'tbl_categories.id')
            ->leftjoin('tbl_product_issue', 'tbl_product_issue.id', '=', 'tbl_product_issue_lists.issue_id')
            ->leftjoin('tbl_lifting_products', 'tbl_lifting_products.serial_no', '=', 'tbl_product_issue_lists.serial_no');

        if ($category_id) {
            $sales = $sales->whereIn('tbl_products.category_id', $category_id);
        }

        if ($product_id) {
            $sales = $sales->whereIn('tbl_products.id', $product_id);
        }
        $sales = $sales->where('tbl_product_issue_lists.company_id', $this->company)
            ->where('tbl_product_issue.product_type', $type)
            ->where('tbl_product_issue.date', '>=', $fromDate)
            ->where('tbl_product_issue.date', '<=', $toDate)
            ->groupBy('tbl_product_issue_lists.product_id')
            ->get();


        $pdf = PDF::loadView('admin.productWiseProfit.print', [
            'title' => $title, 'fromDate' => $fromDate,
            'toDate' => $toDate,
            'sales' => $sales,
            'type' => $type
        ], [], ['orientation' => 'L']);

        $pdf->stream('product_wise_profit' . $fromDate . '_' . $toDate . '.pdf');
    }
}
