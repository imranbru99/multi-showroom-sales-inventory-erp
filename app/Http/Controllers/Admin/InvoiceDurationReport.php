<?php

namespace App\Http\Controllers\admin;

use PDF;
use Carbon\Carbon;
use App\StaffSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Collection\UpComingCollection;
use App\Services\RetailSale\Report\InvoiceDuration;

class InvoiceDurationReport extends Controller
{

    public function index(Request $request)
    {

        $title = "Invoice Duration Report";
        $searchFormLink = "invoice.duration.index";
        $printFormLink = "invoice.duration.print";

        $start_date = Carbon::now()->format('d-m-Y');
        $end_date = Carbon::now()->format('d-m-Y');
        $staffs = StaffSetup::where('status', 1)->get();

        if ($request->has('start_date')) {
            $start_date = date('d-m-Y', strtotime($request->start_date));
        }

        if ($request->has('end_date')) {
            $end_date = date('d-m-Y', strtotime($request->end_date));
        }

        $staffId = $request->staff;

        $retailSales = [];

        if ($request->searched) {
            $retailSales = InvoiceDuration::Report($start_date, $end_date, $staffId);
        }

        // dd($retailSales);

        return view('admin.invoiceDuration.index')->with(compact('staffs', 'staffId', 'title', 'searchFormLink', 'printFormLink', 'start_date', 'end_date', 'retailSales'));
    }


    public function print(Request $request)
    {

        // dd($request);

        $start_date = Carbon::now()->format('d-m-Y');
        $end_date = Carbon::now()->format('d-m-Y');


        if ($request->start_date) {
            $start_date = date('d-m-Y', strtotime($request->start_date));
        }

        if ($request->end_date) {
            $end_date = date('d-m-Y', strtotime($request->end_date));
        }

        $staffId = $request->staffId;

        $title = "Invoice Duration Report From " . $start_date . ' To ' . $end_date . '. ';

        if($staffId){
            $staff = StaffSetup::find($staffId);
            $title .= "Employee Name: " . $staff->name;
        }

        $retailSales = InvoiceDuration::Report($start_date, $end_date, $staffId);

        $pdf = PDF::loadView('admin.invoiceDuration.print', ['start_date' => $start_date, 'end_date' => $end_date, 'title' => $title, 'retailSales' => $retailSales], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('invoice_duration.pdf');
    }
}
