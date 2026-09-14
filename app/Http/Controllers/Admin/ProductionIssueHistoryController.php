<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\CategorySetup;
use App\StaffSetup;
use DB;
use MPDF;
use PDF;

class ProductionIssueHistoryController extends Controller
{

    

    public function index(Request $request)
    {
        $title = "Production Sales History";
        $searchFormLink = "productionIssueHistory.index";
        $printFormLink = "productionIssueHistory.print";

        $staff = $request->staff;
        $category = $request->category;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $staffs = StaffSetup::where('showroom_id', $this->showroomId)->where('status', '1')
            ->orderBy('name', 'asc')
            ->get();

        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->get();

        

        $productIssueHistories = array();
   

        $productIssueHistories = DB::table('view_production_issue_history')
            ->select('date', 'staffId', 'staffName', 'categoryId', 'categoryName', 'productId', 'productName', 'modelNo', DB::raw('GROUP_CONCAT(productSerialNO) as totalProductSerialNO'), DB::raw('SUM(issueQty) as totalIssueQty'))
            ->orWhere(function ($query) use ($fromDate, $toDate, $staff, $category) {
                if (!empty($fromDate)) {
                    $query->whereBetween('date', array($fromDate, $toDate));
                }

                if ($staff) {
                    $query->whereIn('staffId', $staff);
                }

                if ($category) {
                    $query->whereIn('categoryId', $category);
                }
                
                
            })
            ->where('showroomId', $this->showroomId)
            ->orderBy('date', 'desc')
            ->groupBy('productId')->groupBy('modelNo')->groupBy('staffId')->groupBy('date')
            ->get();

        return view('admin.productionIssueHistory.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'staffs', 'categories', 'staff', 'category', 'fromDate', 'toDate', 'productIssueHistories'));
    }

    public function print(Request $request)
    {
        $title = "Production Sales History";

         $staff = $request->staff;
        $category = $request->category;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $dealers = StaffSetup::where('showroom_id', $this->showroomId)->where('status', '1')
            ->orderBy('name', 'asc')
            ->get();

        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->get();

     

        $productIssueHistories = array();

       
        $productIssueHistories = DB::table('view_production_issue_history')
            ->select('date','staffId','staffName', 'categoryId', 'categoryName', 'productId', 'productName', 'modelNo', DB::raw('GROUP_CONCAT(productSerialNO) as totalProductSerialNO'), DB::raw('SUM(issueQty) as totalIssueQty'))
            ->orWhere(function ($query) use ($fromDate, $toDate, $staff, $category) {
                if (!empty($fromDate)) {
                    $query->whereBetween('date', array($fromDate, $toDate));
                }

                if ($staff) {
                    $query->whereIn('staffId', $staff);
                }

                if ($category) {
                    $query->whereIn('categoryId', $category);
                }
                
              
            })
            ->where('showroomId', $this->showroomId)
            ->orderBy('date', 'desc')
            ->groupBy('productId')->groupBy('modelNo')->groupBy('staffId')->groupBy('date')
            ->get();

        $pdf = PDF::loadView('admin.productionIssueHistory.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'productIssueHistories' => $productIssueHistories]);

        return $pdf->stream('production_issue_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }


    
}
