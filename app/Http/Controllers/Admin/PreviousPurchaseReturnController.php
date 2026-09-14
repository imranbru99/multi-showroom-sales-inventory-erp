<?php

namespace App\Http\Controllers\admin;

use App\PrevPurchaseReturn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;

class PreviousPurchaseReturnController extends Controller
{
    
    public function index(Request $request)
    {
    	$title = "Previous Purchase Return Report";
    	$searchFormLink = "prevPurchaseReturn.index";
    	$printFormLink = "prevPurchaseReturn.print";

    	$searchType = $request->searchType;
    	$searchText = $request->searchText;
    	$fromDate = date('Y-m-d',strtotime($request->fromDate));
    	$toDate = date('Y-m-d',strtotime($request->toDate));

        $print = $request->print;


        if(!$print){
            $prvPurchaseReturns = PrevPurchaseReturn::all();
          
        }else{
            $prvPurchaseReturns = PrevPurchaseReturn::orWhere(function($query) use($fromDate,$toDate,$searchType,$searchText){
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
          
            })
            ->orderBy('date','asc')
            ->select(['date', 'invoice_no', 'vendor_name', 'product_name','model_no','qty', 'mrp_tk', 'cp_tk', 'discount_receipt', 'invoice_amount', 'total_amount'])
            ->get();


            $data = (object) [
                'total_quantity' => $prvPurchaseReturns->sum('qty'),
                'total_mrp_tk' => $prvPurchaseReturns->sum('mrp_tk'),
                'total_cp_tk' => $prvPurchaseReturns->sum('cp_tk'),
                'total_discount_receipt' => $prvPurchaseReturns->sum('discount_receipt'),
                'total_amount' => $prvPurchaseReturns->sum('total_amount'),
            ];
        }



        $data = (object) [
                'total_quantity' => $prvPurchaseReturns->sum('qty'),
                'total_mrp_tk' => $prvPurchaseReturns->sum('mrp_tk'),
                'total_cp_tk' => $prvPurchaseReturns->sum('cp_tk'),
                'total_discount_receipt' => $prvPurchaseReturns->sum('discount_receipt'),
                'total_invoice_amount' => $prvPurchaseReturns->sum('invoice_amount'),
                'total_total_amount' => $prvPurchaseReturns->sum('total_amount'),
        ];
            


        return view('admin.previous_purchase_return.index')->with(compact('title','searchFormLink','printFormLink','print','fromDate','toDate','searchType','searchText','prvPurchaseReturns','data'));
    }



}
