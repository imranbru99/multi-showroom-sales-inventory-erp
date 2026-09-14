<?php

namespace App\Http\Controllers\admin;

use App\PrevPurchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\DB;

class PreviousPurchaseController extends Controller
{
    
    public function index(Request $request)
    {
    	$title = "Previous Purchase Report";
    	$searchFormLink = "prev_purchase.index";
    	$printFormLink = "prev_purchase.print";

    	$searchType = $request->searchType;
    	$searchText = $request->searchText;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));
        $vendors = $request->vendors;


        // $fromDate = DateTime::createFromFormat('Y-m-d H:i:s', '$fromDate 00:00:00');
        // if ($fromDate === false) {
        //     die("Incorrect date string");
        // } else {
        //     $fromDate = $fromDate->getTimestamp();
        // }

        // dd($fromDate);

        // dd($request->fromDate);

    	// $fromDate = $request->fromDate;
    	// $toDate = $request->toDate;
        $print = $request->print;

        // $retailSalesReports = RetailSalesReport::select(['qty','sales_amount','collection','discount','gift_voucher','exchange_crt','total_balance','agreement_tk'])->get();

        // dd($fromDate);

        if(!$print){
            $retailSalesReports = PrevPurchase::all();
            // $retailSalesReports = [];
            // $data = [];
        }else{
            $retailSalesReports = PrevPurchase::orWhere(function($query) use($fromDate,$toDate,$searchType,$searchText, $vendors){
                if (!empty($fromDate))
                {
                    // dd($fromDate);
                    // $query->whereBetween('date', array($fromDate,$toDate));
                    // $query->where('date', '>=' ,$fromDate);
                    // $query->where('date', '<=' ,$toDate);
                }

               if ($searchType) {
                    $like_columns = ['vendor_name', 'product_name', 'model_no'];

                    foreach ($searchType as $searchval) {
                        
                        $searchText = strtolower($searchText);
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
             
                    $query->where('vendor_name',$vendor);
                
                    }
                }

                // dd($query);
            })
            ->orderBy('date','asc')
            ->select(['date', 'invoice_no', 'vendor_name', 'product_name','model_no','qty', 'mrp_tk', 'cp_tk', 'discount_receipt', 'invoice_amount', 'total_amount'])
            ->get();


            $data = (object) [
                'total_quantity' => $retailSalesReports->sum('qty'),
                'total_mrp_tk' => $retailSalesReports->sum('mrp_tk'),
                'total_cp_tk' => $retailSalesReports->sum('cp_tk'),
                'total_discount_receipt' => $retailSalesReports->sum('discount_receipt'),
                'total_amount' => $retailSalesReports->sum('total_amount'),
            ];
        }



        // foreach ($retailSalesReports as $retailSalesReport) {
        //     if(!is_numeric($retailSalesReport->discount)){
        //         dd($retailSalesReport);
        //     }
        // }

        $data = (object) [
                'total_quantity' => $retailSalesReports->sum('qty'),
                'total_mrp_tk' => $retailSalesReports->sum('mrp_tk'),
                'total_cp_tk' => $retailSalesReports->sum('cp_tk'),
                'total_discount_receipt' => $retailSalesReports->sum('discount_receipt'),
                'total_invoice_amount' => $retailSalesReports->sum('invoice_amount'),
                'total_total_amount' => $retailSalesReports->sum('total_amount'),
        ];
            

       
        $allVendor = DB::table('tbl_previous_purchase')->groupby('vendor_name')->get();


        return view('admin.previous_purchase.index')->with(compact('title','searchFormLink','printFormLink','print','fromDate','toDate','searchType','searchText','retailSalesReports','data', 'allVendor'));
    }



}
