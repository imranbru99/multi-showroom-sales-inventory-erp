<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\CustomerRegistrationSetup;
use App\Product;
use App\ProductIssueList;
use App\RetailSales;

use DB;
use PDF;
use Carbon\Carbon;

class WarrantyController extends Controller
{



    public function index(Request $request)
    {
        $title = "Warranty";
        $searchFormLink = "warranty.index";
        $printFormLink = "warranty.print";

        $print = $request->print;
        $customer_id = $request->customer_id;



        $customers = CustomerRegistrationSetup::where('showroom_id', $this->showroomId)->get();

        $warrantyProducts = [];

        if ($request->customer_id) {
            $warrantyProducts = RetailSales::with('sale', 'product')
                ->whereHas('sale', function ($q) use ($request) {
                    $q->where('customer_id', $request->customer_id);
                })
                ->get();
        }


        return view('admin.warranty.index')->with(compact(
            'title',
            'searchFormLink',
            'printFormLink',
            'print',
            'customer_id',
            'customers',
            'warrantyProducts',
        ));
    }

    public function getProductInfo(Request $request)
    {
        $title = "Warranty";
        $product = RetailSales::with('sale', 'sale.customer', 'sale.seller', 'product')->where('product_serial', $request->serial_no)->first();
        if ($product) {
            $type = 'retail';
            $warranty_status = 'None';
            if (!empty($product)) {
                $warranty_month = $product->product->warranty;
                if ($warranty_month) {
                    $saleDate = $product->sale->sale_date;
                    $warranty_limit = date('Y-m-d', strtotime("+" . $warranty_month . " months", strtotime($saleDate)));

                    if ($warranty_limit >= date('Y-m-d')) {
                        $warranty_status = 'End on ' . date('d-m-Y', strtotime($warranty_limit));
                    } else {
                        $warranty_status = 'End';
                    }
                }
            }
        } else {
            $type = 'dealer';
            $product = ProductIssueList::with('issue', 'issue.dealer', 'product', 'issue.SalesBy')->where('serial_no', $request->serial_no)->first();

            $warranty_status = 'Yes';
            // if (!empty($product)) {
            //     $warranty_month = $product->product->warranty;
            //     if ($warranty_month) {
            //         $saleDate = $product->issue->date;
            //         $warranty_limit = date('Y-m-d', strtotime("+" . $warranty_month . " months", strtotime($saleDate)));

            //         if ($warranty_limit >= date('Y-m-d')) {
            //             $warranty_status = 'End on ' . date('d-m-Y', strtotime($warranty_limit));
            //         } else {
            //             $warranty_status = 'End';
            //         }
            //     }
            // }
        }

        if (!empty($product)) {
            $status = 'true';
        } else {
            $status = 'false';
        }



        return $data = [
            'product' => $product,
            'type' => $type,
            'warranty_status' => $warranty_status,
            'status' => $status,
        ];
    }
}
