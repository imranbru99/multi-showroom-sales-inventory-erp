<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\RetailSalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;
// use DB;

class RetailSalesReportController extends Controller
{
    public function index(Request $request)
    {

        $title = "Previous Retail Sales Report";
        $searchFormLink = "retailSalesReport.index";
        $printFormLink = "retailSalesReport.print";

        $vendors = $request->vendors;
        $dealers = $request->dealers;
        $searchType = $request->searchType;
        $searchText = $request->searchText;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));



        $fromDateToform = $request->fromDate;
        $toDateToform = $request->toDate;
        $print = $request->print;

        if (!$print) {
            
            $retailSalesReports = [];
            $data = [];
        } else {


            $retailSalesReports = RetailSalesReport::orWhere(function ($query) use ($fromDate, $toDate, $searchType, $searchText, $vendors, $dealers) {
                if (!empty($fromDate)) {
                   
                }

                if ($searchType) {
                    $like_columns = ['customer_name', 'employee_name', 'groups', 'product_name', 'model_no'];

                    foreach ($searchType as $searchval) {

                        $i = 0;
                        if ($i > 0) {
                          if (in_array($searchval, $like_columns)) {

                            $query->where($searchval, 'like', '%' . $searchText . '%');

                        }else{

                            $query->where($searchval, $searchText);

                        }

                        $i++;

                    }else{

                       if (in_array($searchval, $like_columns)) {

                            $query->orWhere($searchval, 'like', '%' . $searchText . '%');

                    }else{

                            $query->orWhere($searchval, $searchText);
                    }
                }

            }
        }

        if ($vendors) {

            foreach ($vendors as $vendor) {
             
                $query->where('vendor_id',$vendor);
                
            }
        }

         if ($dealers) {

            foreach ($dealers as $dealer) {
             
                $query->where('dealer_id',$dealer);
                
            }
        }


    })

            ->orderBy('date', 'asc')
            ->select(['date', 'customer_name', 'account_no', 'product_name', 'model_no', 'qty', 'money_receipt_no', 'price', 'sales_amount', 'collection', 'discount', 'gift_voucher', 'exchange_crt', 'total_balance', 'agreement_tk', 'mobile_no', 'memo_no'])
            // ->groupBy('account_no')
            ->where('showroom_id', $this->showroomId)
            ->get();


            $total_balance = $retailSalesReports->sum('sales_amount') -  $retailSalesReports->sum('collection') - $retailSalesReports->sum('discount');


                   // dd($retailSalesReports);

            $data = (object) [
                'total_quantity' => $retailSalesReports->sum('qty'),
                'total_sales_price' => $retailSalesReports->sum('sales_amount'),
                'total_collection' => $retailSalesReports->sum('collection'),
                'total_balance' => $total_balance,
                'total_discount' => $retailSalesReports->sum('discount'),
                'total_gift_voucher' => $retailSalesReports->sum('gift_voucher'),
                'total_exchange_crt' => $retailSalesReports->sum('exchange_crt'),
                'total_agreement_tk' => $retailSalesReports->sum('agreement_tk'),
            ];
        }



       

        $allVendor = DB::table('tbl_vendors')->get();
        $allShowroomProject = DB::table('tbl_showroomprojects')->where('showroom_id', $this->showroomId)->orderBy('id', 'desc')->get();



        return view('admin.retailSalesReport.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDateToform', 'toDateToform', 'searchType', 'searchText', 'retailSalesReports', 'data','allVendor', 'allShowroomProject'));
    }

    function print(Request $request) {
        $title = "Print Previous Retail Sales Report";
        $vendors = $request->vendors;
        $dealers = $request->dealers;
        $searchType = $request->searchType;
        $searchText = $request->searchText;

       

        $fromDate = $request->fromDate;
        $toDate = $request->toDate;

        $retailSalesReports = array();

        $retailSalesReports = RetailSalesReport::orWhere(function ($query) use ($fromDate, $toDate, $searchType, $searchText, $vendors, $dealers) {
            if (!empty($fromDate)) {
                $query->whereBetween('date', array($fromDate, $toDate));
            }

            if ($searchType) {
               

                foreach ($searchType as $searchval) {
                    $query->orWhere($searchval, 'like', '%' . $searchText . '%');
                }
            }


            if ($vendors) {

                foreach ($vendors as $vendor) {
                 
                    $query->where('vendor_id',$vendor);
                    
                }
            }

            if ($dealers) {

                foreach ($dealers as $dealer) {
                 
                    $query->where('dealer_id',$dealer);
                    
                }
            }
        })
        ->orderBy('id', 'asc')
        ->get();


        $pdf = PDF::loadView('admin.retailSalesReport.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'retailSalesReports' => $retailSalesReports], [], ['orientation' => 'L']);

        return $pdf->stream('previous_retail_sales_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }


    public function outdstanding_report(Request $request)
    {

        $title = "Outstanding Sales Report";
        $searchFormLink = "outstandingSalesReport.index";
        $printFormLink = "outstandingSalesReport.print";

        $vendors = $request->vendors;
        $dealers = $request->dealers;
        $searchType = $request->searchType;
        $searchText = $request->searchText;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

    // dd($dealers);

        $fromDateToform = $request->fromDate;
        $toDateToform = $request->toDate;
        $print = $request->print;

        if (!$print) {
           
            $retailSalesReports = [];
            $data = [];
        } else {


        if($dealers == 101){

            

                $retailSalesReports = DB::table('tbl_previous_retail_sales')
                ->select('dealer_id', 'account_no','mobile_no','showroom_id', DB::raw("TRIM(customer_name) as name"),  DB::raw('(SUM(sales_amount)) as sales'),  DB::raw('(SUM(discount)) as discount'), DB::raw('(SUM(gift_voucher)) as gift_voucher'), DB::raw('(SUM(exchange_crt)) as exchange_crt'), DB::raw('(SUM(agreement_tk)) as agreement_tk'), DB::raw('SUM(collection) as collection'))->orWhere(function($query) use($fromDate,$toDate,$dealers){
                   

                    if (@$dealers)
                    {
                        $query->where('dealer_id',$dealers);
                    }
                })

                ->where('showroom_id',$this->showroomId)

                ->groupBy('name')

                ->orderBy('name')

                ->get();


        }else{

                $retailSalesReports = DB::table('tbl_previous_retail_sales')
                ->select('dealer_id', 'account_no','mobile_no','showroom_id', DB::raw("TRIM(customer_name) as name"),  DB::raw('(SUM(sales_amount)) as sales'),  DB::raw('(SUM(discount)) as discount'), DB::raw('(SUM(gift_voucher)) as gift_voucher'), DB::raw('(SUM(exchange_crt)) as exchange_crt'), DB::raw('(SUM(agreement_tk)) as agreement_tk'), DB::raw('SUM(collection) as collection'))->orWhere(function($query) use($fromDate,$toDate,$dealers){
                   

                    if (@$dealers)
                    {
                        $query->where('dealer_id',$dealers);
                    }
                })

                ->where('showroom_id',$this->showroomId)

                ->groupBy('account_no')

                ->orderBy('name')

                ->get();
        }

            $total_balance = $retailSalesReports->sum('sales') - $retailSalesReports->sum('discount') - $retailSalesReports->sum('gift_voucher')- $retailSalesReports->sum('exchange_crt')-  $retailSalesReports->sum('collection');


            $data = (object) [
                'total_quantity' => $retailSalesReports->sum('qty'),
                'total_sales_price' => $retailSalesReports->sum('sales'),
                'total_collection' => $retailSalesReports->sum('collection'),
                'total_balance' => $total_balance,
                'total_discount' => $retailSalesReports->sum('discount'),
                'total_gift_voucher' => $retailSalesReports->sum('gift_voucher'),
                'total_exchange_crt' => $retailSalesReports->sum('exchange_crt'),
                'total_agreement_tk' => $retailSalesReports->sum('agreement_tk'),
            ];
        }



        $allVendor = DB::table('tbl_vendors')->get();
        $allShowroomProject = DB::table('tbl_showroomprojects')->where('showroom_id', $this->showroomId)->orderBy('id', 'desc')->get();



        return view('admin.retailSalesReport.outstandingSalesReport')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDateToform', 'toDateToform', 'searchType', 'searchText', 'retailSalesReports', 'data', 'allShowroomProject'));
    }





}
