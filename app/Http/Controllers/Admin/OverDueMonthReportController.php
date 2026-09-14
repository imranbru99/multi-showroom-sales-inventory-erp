<?php

namespace App\Http\Controllers\admin;

use PDF;
use Carbon\Carbon;
use App\StaffSetup;
use App\ShowroomProjectSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Collection\UpComingCollection;
use App\Services\RetailSale\Report\InvoiceDuration;

class OverDueMonthReportController extends Controller
{
    public function index(Request $request)
    {

        $title = "Over Due Month Report";
        $searchFormLink = "overDueMonthReport.index";
        $printFormLink = "overDueMonthReport.print";

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;

        $start_date = date('Y-m-d', strtotime(now()->subDays(30)));

        $retailSales = [];

        if ($request->searched) {
            $retailSales = InvoiceDuration::overDue($start_date, $showroom_id, $project_id, $staffId);
        }

        return view('admin.overDueMonthReport.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'retailSales'));
    }


    public function print(Request $request)
    {
        $title = "Over Due Month Report";

        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;

        $staff = StaffSetup::where('id', $staffId)->first();

        $start_date = date('Y-m-d', strtotime(now()->subDays(30)));


        $retailSales = InvoiceDuration::overDue($start_date, $showroom_id, $project_id, $staffId);


        $pdf = PDF::loadView('admin.overDueMonthReport.print', ['title' => $title, 'retailSales' => $retailSales, 'staff' => $staff], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('overdue.pdf');
    }
}
