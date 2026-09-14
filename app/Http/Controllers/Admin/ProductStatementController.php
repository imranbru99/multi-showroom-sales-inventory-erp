<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Product;

use DB;
use PDF;
use MPDF;

class ProductStatementController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Product Statement Report";
    	$searchFormLink = "productStatement.index";
    	$printFormLink = "productStatement.print";

    	$product = $request->product;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));
        $print = $request->print;

        $lastDate = Date('Y-m-d',strtotime("-1 day", strtotime($fromDate)));

    	$products = Product::where('status','1')
    		->orderBy('name','asc')
    		->get();

        $openingBalance = DB::table('view_product_statement')
            ->select(DB::raw('((SUM(liftingPrice) + SUM(productReturnPrice) + SUM(slaesReturnPrice)) - (SUM(liftingReturnPrice) + SUM(productIssuePrice) + SUM(salesPrice))) as opening'))
            ->orWhere(function($query) use($lastDate,$product){
                if (!empty($lastDate))
                {
                    $query->where('date','<=', $lastDate);
                }

                if (@$product)
                {
                    $query->where('productId',$product);
                }
            })
            ->where('showroomId',$this->showroomId)
            ->first();

        $productStatements = DB::table('view_product_statement')
            ->select('date as date','productId as productId','productName as productName', DB::raw('SUM(liftingPrice) as liftingPrice'), DB::raw('SUM(liftingReturnPrice) as liftingReturnPrice'), DB::raw('sum(productIssuePrice) as productIssuePrice'),DB::raw('SUM(productReturnPrice) as productReturnPrice'),DB::raw('SUM(salesPrice) as salesPrice'),DB::raw('SUM(slaesReturnPrice) as slaesReturnPrice'))
            ->orWhere(function($query) use($fromDate,$toDate,$product){
                if (!empty($fromDate))
                {
                    $query->whereBetween('date', array($fromDate,$toDate));
                }

                if (@$product)
                {
                    $query->where('productId',$product);
                }
            })
            ->where('showroomId',$this->showroomId)
            ->groupBy('date')
            ->get();

    	return view('admin.productStatement.index')->with(compact('title','searchFormLink','printFormLink','print','products','product','fromDate','toDate','openingBalance','productStatements'));
    }

    public function print(Request $request)
    {
    	$title = "Product Statement Report";

    	$product = $request->product;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));

        $lastDate = Date('Y-m-d',strtotime("-1 day", strtotime($fromDate)));

        $productName = Product::where('id',$product)->first();

        $openingBalance = DB::table('view_product_statement')
            ->select(DB::raw('((SUM(liftingPrice) + SUM(productReturnPrice) + SUM(slaesReturnPrice)) - (SUM(liftingReturnPrice) + SUM(productIssuePrice) + SUM(salesPrice))) as opening'))
            ->orWhere(function($query) use($lastDate,$product){
                if (!empty($lastDate))
                {
                    $query->where('date','<=', $lastDate);
                }

                if (@$product)
                {
                    $query->where('productId',$product);
                }
            })
            ->where('showroomId',$this->showroomId)
            ->first();

        $productStatements = DB::table('view_product_statement')
            ->select('date as date','productId as productId','productName as productName', DB::raw('SUM(liftingPrice) as liftingPrice'), DB::raw('SUM(liftingReturnPrice) as liftingReturnPrice'), DB::raw('sum(productIssuePrice) as productIssuePrice'),DB::raw('SUM(productReturnPrice) as productReturnPrice'),DB::raw('SUM(salesPrice) as salesPrice'),DB::raw('SUM(slaesReturnPrice) as slaesReturnPrice'))
            ->orWhere(function($query) use($fromDate,$toDate,$product){
                if (!empty($fromDate))
                {
                    $query->whereBetween('date', array($fromDate,$toDate));
                }

                if (@$product)
                {
                    $query->where('productId',$product);
                }
            })
            ->where('showroomId',$this->showroomId)
            ->groupBy('date')
            ->get();

        $pdf = PDF::loadView('admin.productStatement.print',['title'=>$title,'fromDate'=>$fromDate,'toDate'=>$toDate,'productName'=>$productName,'openingBalance'=>$openingBalance,'productStatements'=>$productStatements]);

        return $pdf->stream('product_statement_report_'.$fromDate.'_to_'.$toDate.'.pdf');
    }
}
