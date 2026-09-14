<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\VendorSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Statement\Vendor\Statement;

class VendorStatementController extends Controller {

    public function index(Request $request) {

        if ($request->print) {
            if (!$request->vendor[0]) {
                return back()->with('fail_msg', 'Please Select Any Vendor');
            }
        }


        $title = "Vendor Statement";
        $searchFormLink = "vendorStatement.index";
        $printFormLink = "vendorStatement.print";

        $vendor = $request->vendor;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $vendors = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $data = [];

        if ($request->print) {

            $previousBalance = Statement::previousBalance($vendor, $fromDate);

            $statements = Statement::Statement($vendor, $fromDate, $toDate, $previousBalance);

            $data = [
                'previousBalance' => $previousBalance,
                'statements' => $statements,
            ];
        }


        return view('admin.vendorStatement.index')->with(compact('data', 'title', 'searchFormLink', 'printFormLink', 'print', 'vendors', 'vendor', 'fromDate', 'toDate'));
    }

    public function print(Request $request) {
        $title = "Print Vendor Statement";
        $vendor = $request->vendor;
        $vendorName = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $vendor[0])
                ->first();

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $data = [];


        $previousBalance = Statement::previousBalance($vendor, $fromDate);

        $statements = Statement::Statement($vendor, $fromDate, $toDate, $previousBalance);

        $data = [
            'previousBalance' => $previousBalance,
            'statements' => $statements,
        ];

        $pdf = PDF::loadView('admin.vendorStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'data' => $data, 'vendorName' => $vendorName]);

        return $pdf->stream('vendor_statement_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
