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

class OverDueScheduleController extends Controller
{
    public function index(Request $request)
    {
        // ini_set('max_execution_time', '500');
        // ini_set('max_input_time', '500');
        // ini_set('memory_limit', '60MB');
        //         max_execution_time = 30;// Maximum execution time of each script, in seconds
        // max_input_time = 60 ; //Maximum amount of time each script may spend parsing request data
        // memory_limit = 8M      ; //Maximum amount of memory a script may consume (8MB)

        $title = "Over Due Report";
        $searchFormLink = "overDueSchedule.index";
        $printFormLink = "overDueSchedule.print";

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;

        $days = $request->duration;

        $start_date = date('Y-m-d', strtotime(now()->subDays($days)));

        $retailSales = [];

        if ($request->searched) {
            $retailSales = InvoiceDuration::overDueSchedule($showroom_id, $project_id, $staffId, $start_date);
        }

        return view('admin.overDueSchedule.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'retailSales', 'days'));
    }


    public function print(Request $request)
    {
        $title = "Over Due Report";

        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;

        $days = $request->duration;

        $start_date = date('Y-m-d', strtotime(now()->subDays($days)));

        $staff = StaffSetup::where('id', $staffId)->first();


        $retailSales = InvoiceDuration::overDueSchedule($showroom_id, $project_id, $staffId);


        $pdf = PDF::loadView('admin.overDueSchedule.print', ['title' => $title, 'retailSales' => $retailSales, 'staff' => $staff, 'days' => $days], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('overdue.pdf');
    }
}
