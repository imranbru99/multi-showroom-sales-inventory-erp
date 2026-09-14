<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\InvoiceReport;
use PDF;
use DB;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Previous Invoice Report";
    	$searchFormLink = "invoiceReport.index";
    	$printFormLink = "invoiceReport.print";

    	$searchType = $request->searchType;
    	$searchText = $request->searchText;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));
        $print = $request->print;

        $invoiceReports = array();

        $invoiceReports = InvoiceReport::select('tbl_previous_invoices.*',DB::raw('SUM(invoice_amount) as totalAmount'))
        	->orWhere(function($query) use($fromDate,$toDate,$searchType,$searchText){
                if (!empty($fromDate))
                {
                    $query->whereBetween('date', array($fromDate,$toDate));
                }

                if ($searchType)
                {
                    $query->where($searchType,'like','%'.$searchText.'%');
                }
            })
            ->groupBy('challan_no')
            ->orderBy('id','asc')
            ->get();

        // dd($retailSalesReports);

        return view('admin.invoiceReport.index')->with(compact('title','searchFormLink','printFormLink','print','fromDate','toDate','searchType','searchText','invoiceReports'));
    }

    public function print(Request $request)
    {
    	$title = "Print Previous Retail Sales Report";

    	$searchType = $request->searchType;
    	$searchText = $request->searchText;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));
        $print = $request->print;

        $invoiceReports = array();

        $invoiceReports = InvoiceReport::select('tbl_previous_invoices.*')
        	->orWhere(function($query) use($fromDate,$toDate,$searchType,$searchText){
                if (!empty($fromDate))
                {
                    $query->whereBetween('date', array($fromDate,$toDate));
                }

                if ($searchType)
                {
                    $query->where($searchType,'like','%'.$searchText.'%');
                }
            })
            ->orderBy('id','asc')
            ->get();

        $pdf = PDF::loadView('admin.invoiceReport.print',['title'=>$title,'fromDate'=>$fromDate,'toDate'=>$toDate,'retailSalesReports'=>$invoiceReports],[],['orientation'=>'L']);

        return $pdf->stream('previous_invoice_report_'.$fromDate.'_to_'.$toDate.'.pdf');
    }

    public function details(Request $request)
    {
    	$invoiceDetails = InvoiceReport::where('challan_no','=',$request->challanNo)->get();
    	$totalInvoiceDetails = InvoiceReport::select('tbl_previous_invoices.*',DB::raw('SUM(invoice_amount) as totalAmount'))
    		->where('challan_no','=',$request->challanNo)
            ->groupBy('challan_no')
            ->orderBy('id','asc')
    		->first();
        
        if($request->ajax())
        {
            return response()->json([
                'invoiceDetails'=>$invoiceDetails,
                'totalInvoiceDetails'=>$totalInvoiceDetails
            ]);
        }
    }
}
