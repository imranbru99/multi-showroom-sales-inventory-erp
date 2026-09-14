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

class OverDueReportController extends Controller
{
    public function index(Request $request)
    {

        $title = "Over Due Report";
        $searchFormLink = "overDueReport.index";
        $printFormLink = "overDueReport.print";

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;

        $start_date = date('Y-m-d', strtotime(now()->subDays(365)));

        $retailSales = [];

        if ($request->searched) {
            $retailSales = InvoiceDuration::overDue($start_date, $showroom_id, $project_id, $staffId);
        }

        return view('admin.overDueReport.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'retailSales'));
    }


    public function print(Request $request)
    {
        $title = "Over Due Report";

        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;

        $staff = StaffSetup::where('id', $staffId)->first();

        $start_date = date('Y-m-d', strtotime(now()->subDays(365)));


        $retailSales = InvoiceDuration::overDue($start_date, $showroom_id, $project_id, $staffId);


        $pdf = PDF::loadView('admin.overDueReport.print', ['title' => $title, 'retailSales' => $retailSales, 'staff' => $staff], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('overdue.pdf');
    }
}
