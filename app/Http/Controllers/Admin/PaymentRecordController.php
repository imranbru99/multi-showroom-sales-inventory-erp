<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorSetup;
use App\PaymentToCompany;
use DB;
use PDF;
use MPDF;

class PaymentRecordController extends Controller {

    public function index(Request $request) {
        $title = "Payment History";
        $searchFormLink = "paymentRecord.index";
        $printFormLink = "paymentRecord.print";

        $vendor = $request->vendor;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $vendors = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $btnSummary = $request->btnSummary;
        $btnRecord = $request->btnRecord;

        $productRecords = [];
        $paymentSummaries = [];

        if ($request->btnSummary == "Summary") {
            $paymentSummaries = PaymentToCompany::select(
                            'tbl_vendors.name as vendorName',
                            DB::raw('SUM(tbl_payment_to_company.payment_now) as price')
                    )
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->orWhere(function($query) use($fromDate, $toDate, $vendor) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_payment_to_company.payment_date', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('tbl_payment_to_company.vendor_id', $vendor);
                        }
                    })
                    ->where('tbl_payment_to_company.company_id', $this->company)
//                ->where('tbl_payment_to_company.showroom_id', $this->showroomId)
                    ->orderBy('tbl_vendors.name', 'asc')
                    ->groupBy('vendor_id')
                    ->get();
        }

        if ($request->btnRecord == "Record") {
            $productRecords = PaymentToCompany::select(
                            'tbl_payment_to_company.payment_date as paymentDate',
                            'tbl_payment_to_company.payment_now as price',
                            'tbl_vendors.name as vendorName',
                            'tbl_payment_to_company.payment_no as paymentNo',
                            'tbl_payment_to_company.payment_type as paymentType',
                            'tbl_payment_to_company.remarks as remarks'
                    )
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->orWhere(function($query) use($fromDate, $toDate, $vendor) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_payment_to_company.payment_date', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('tbl_payment_to_company.vendor_id', $vendor);
                        }
                    })
                    ->where('tbl_payment_to_company.company_id', $this->company)
//                ->where('tbl_payment_to_company.showroom_id', $this->showroomId)
                    ->orderBy('tbl_payment_to_company.payment_date', 'desc')
                    ->get();
        }

        return view('admin.paymentRecord.index')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'vendors',
                                'vendor',
                                'fromDate',
                                'toDate',
                                'print',
                                'productRecords',
                                'paymentSummaries',
                                'btnSummary',
                                'btnRecord'
        ));
    }

    public function print(Request $request) {


        // $title = "Payment Record";

        $title = $request->btnPrintSummary == "Print Summary" ? "Payment Summary" : "Payment Record";

        $vendor = $request->vendor;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $btnPrintSummary = $request->btnPrintSummary;
        $btnPrintRecord = $request->btnPrintRecord;

        $productRecords = [];
        $paymentSummaries = [];

        if ($request->btnPrintSummary == "Print Summary") {
            $paymentSummaries = PaymentToCompany::select(
                            'tbl_vendors.name as vendorName',
                            DB::raw('SUM(tbl_payment_to_company.payment_now) as price')
                    )
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->orWhere(function($query) use($fromDate, $toDate, $vendor) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_payment_to_company.payment_date', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('tbl_payment_to_company.vendor_id', $vendor);
                        }
                    })
                    ->where('tbl_payment_to_company.company_id', $this->company)
//                ->where('tbl_payment_to_company.showroom_id', $this->showroomId)
                    ->orderBy('tbl_vendors.name', 'asc')
                    ->groupBy('vendor_id')
                    ->get();
        }

        if ($request->btnPrintRecord == "Print Record") {
            $productRecords = PaymentToCompany::select(
                            'tbl_payment_to_company.payment_date as paymentDate',
                            'tbl_payment_to_company.payment_now as price',
                            'tbl_vendors.name as vendorName',
                            'tbl_payment_to_company.payment_no as paymentNo',
                            'tbl_payment_to_company.payment_type as paymentType',
                            'tbl_payment_to_company.remarks as remarks'
                    )
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->orWhere(function($query) use($fromDate, $toDate, $vendor) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_payment_to_company.payment_date', array($fromDate, $toDate));
                        }

                        if ($vendor) {
                            $query->whereIn('tbl_payment_to_company.vendor_id', $vendor);
                        }
                    })
                    ->where('tbl_payment_to_company.company_id', $this->company)
//                ->where('tbl_payment_to_company.showroom_id', $this->showroomId)
                    ->orderBy('tbl_payment_to_company.payment_date', 'desc')
                    ->get();
        }
        // dd($productRecords);    

        $pdf = PDF::loadView('admin.paymentRecord.print', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'productRecords' => $productRecords,
                    'paymentSummaries' => $paymentSummaries,
                    'btnPrintSummary' => $btnPrintSummary,
                    'btnPrintRecord' => $btnPrintRecord
        ]);

        $paymentSummaryFileName = "payment_summary_". $fromDate . "_to_" . $toDate .".pdf";
        $paymentRecordFileName = "payment_record_". $fromDate . "_to_" . $toDate .".pdf";

        $file_name = $request->btnPrintSummary == "Print Summary" ? $paymentSummaryFileName : $paymentRecordFileName;
        
        return $pdf->stream($file_name);
    }

}
