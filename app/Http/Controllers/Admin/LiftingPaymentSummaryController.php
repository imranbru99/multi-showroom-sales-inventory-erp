<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\VendorSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Summary\Vendor\VendorSummary;

class LiftingPaymentSummaryController extends Controller {

    public function index(Request $request) {
        $title = "Payment Summary";
        $searchFormLink = "liftingPaymentSummary.index";
        $printFormLink = "liftingPaymentSummary.print";

        $vendor = $request->vendor;
        $year = $request->year;
        $month = $request->month;
        $print = $request->print;

        $vendors = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();



        if ($vendor) {
            $venInformation = VendorSetup::whereIn('id', $vendor)->get();
        } else {
            $venInformation = VendorSetup::where('status', '1')
                    ->where('company_id', $this->company)
                    ->get();
        }

        $data = [];

        if ($request->print) {
            $data = VendorSummary::Summary($year, $month, $venInformation);
        }

        return view('admin.liftingPaymentSummary.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'vendors', 'vendor', 'year', 'month', 'data'));
    }

    public function print(Request $request) {
        $title = "Print Lifting Payment Summary";

        $vendor = $request->vendor;
        $year = $request->year;
        $month = $request->month;

        if ($vendor) {
            $venInformation = VendorSetup::whereIn('id', $vendor)->get();
        } else {
            $venInformation = VendorSetup::where('status', '1')
                    ->where('company_id', $this->company)
                    ->get();
        }

        $data = [];

        if ($request->print) {
            $data = VendorSummary::Summary($year, $month, $venInformation);
        }

        $pdf = PDF::loadView('admin.liftingPaymentSummary.print', ['title' => $title, 'year' => $year, 'month' => $month, 'data' => $data]);

        return $pdf->stream('lifting_payment_summary_' . $year . '_to_' . $month . '.pdf');
    }

}
