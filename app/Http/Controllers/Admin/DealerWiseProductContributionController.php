<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use MPDF;
use PDF;

class DealerWiseProductContributionController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Dealer Wise Product Contribution";
    	$searchFormLink = "dealerWiseProductContribution.index";
    	$printFormLink = "dealerWiseProductContribution.print";

        $searchType = $request->searchType;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));
        $print = $request->print;

        $contributions = array();

        if ($searchType == "Categories")
        {
	        $contributions = DB::table('view_dealer_wise_product_contribution')
	        	->select('date', 'dealerId', 'dealerName', 'categoryId', 'categoryName', DB::raw('SUM(totalIssueQty) as totalIssueQty'), DB::raw('((SUM(totalIssueQty) / (SELECT SUM(totalIssueQty) FROM view_dealer_wise_product_contribution)) * 100) as percentageQty'), DB::raw('SUM(totalIssueAmount) as totalIssueAmount'), DB::raw('((SUM(totalIssueAmount) / (SELECT SUM(totalIssueAmount) FROM view_dealer_wise_product_contribution)) * 100) as percentageAmount'))
	        	->whereBetween('date', array($fromDate,$toDate))
                ->where('showroomId',$this->showroomId)
	        	->groupBy('dealerId','categoryId')
	            ->orderBy('dealerName','asc')
	            ->orderBy('categoryName','asc')
	            ->get();
        }

        if ($searchType == "Products")
        {
	        $contributions = DB::table('view_dealer_wise_product_contribution')
	        	->select('date', 'dealerId', 'dealerName', 'productId', 'productName', DB::raw('SUM(totalIssueQty) as totalIssueQty'), DB::raw('((SUM(totalIssueQty) / (SELECT SUM(totalIssueQty) FROM view_dealer_wise_product_contribution)) * 100) as percentageQty'), DB::raw('SUM(totalIssueAmount) as totalIssueAmount'), DB::raw('((SUM(totalIssueAmount) / (SELECT SUM(totalIssueAmount) FROM view_dealer_wise_product_contribution)) * 100) as percentageAmount'))
	        	->whereBetween('date', array($fromDate,$toDate))
                ->where('showroomId',$this->showroomId)
	        	->groupBy('dealerId','productId')
	            ->orderBy('dealerName','asc')
	            ->orderBy('productName','asc')
	            ->get();
        }

    	return view('admin.dealerWiseProductContribution.index')->with(compact('title','searchFormLink','printFormLink','print','fromDate','toDate','searchType','contributions'));
    }

    public function print(Request $request)
    {
    	$title = "Print Dealer Wise Product Contribution";

        $searchType = $request->searchType;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));

        $contributions = array();

        if ($searchType == "Categories")
        {
	        $contributions = DB::table('view_dealer_wise_product_contribution')
	        	->select('date', 'dealerId', 'dealerName', 'categoryId', 'categoryName', DB::raw('SUM(totalIssueQty) as totalIssueQty'), DB::raw('((SUM(totalIssueQty) / (SELECT SUM(totalIssueQty) FROM view_dealer_wise_product_contribution)) * 100) as percentageQty'), DB::raw('SUM(totalIssueAmount) as totalIssueAmount'), DB::raw('((SUM(totalIssueAmount) / (SELECT SUM(totalIssueAmount) FROM view_dealer_wise_product_contribution)) * 100) as percentageAmount'))
	        	->whereBetween('date', array($fromDate,$toDate))
                ->where('showroomId',$this->showroomId)
	        	->groupBy('dealerId','categoryId')
	            ->orderBy('dealerName','asc')
	            ->orderBy('categoryName','asc')
	            ->get();
        }

        if ($searchType == "Products")
        {
	        $contributions = DB::table('view_dealer_wise_product_contribution')
	        	->select('date', 'dealerId', 'dealerName', 'productId', 'productName', DB::raw('SUM(totalIssueQty) as totalIssueQty'), DB::raw('((SUM(totalIssueQty) / (SELECT SUM(totalIssueQty) FROM view_dealer_wise_product_contribution)) * 100) as percentageQty'), DB::raw('SUM(totalIssueAmount) as totalIssueAmount'), DB::raw('((SUM(totalIssueAmount) / (SELECT SUM(totalIssueAmount) FROM view_dealer_wise_product_contribution)) * 100) as percentageAmount'))
	        	->whereBetween('date', array($fromDate,$toDate))
                ->where('showroomId',$this->showroomId)
	        	->groupBy('dealerId','productId')
	            ->orderBy('dealerName','asc')
	            ->orderBy('productName','asc')
	            ->get();
        }

        $pdf = PDF::loadView('admin.dealerWiseProductContribution.print',['title'=>$title,'fromDate'=>$fromDate,'toDate'=>$toDate,'searchType'=>$searchType,'contributions'=>$contributions]);

        return $pdf->stream('dealer_wise_product_contribution_'.$fromDate.'_to_'.$toDate.'.pdf');
    }
}
