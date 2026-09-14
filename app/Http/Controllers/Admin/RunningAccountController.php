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

class RunningAccountController extends Controller
{
    public function index(Request $request)
    {

        $title = "Running Account";
        $searchFormLink = "runningAccount.index";
        $printFormLink = "runningAccount.print";

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;

        $retailSales = [];

        if ($request->searched) {
            $retailSales = InvoiceDuration::runningAccount($showroom_id, $project_id, $staffId);
        }

        return view('admin.runningAccount.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'retailSales'));
    }


    public function print(Request $request)
    {
        $title = "Running Account";

        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;

        $staff = StaffSetup::where('id', $staffId)->first();


        $retailSales = InvoiceDuration::runningAccount($showroom_id, $project_id, $staffId);


        $pdf = PDF::loadView('admin.runningAccount.print', ['title' => $title, 'retailSales' => $retailSales, 'staff' => $staff], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('closeaccount.pdf');
    }
}
