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

class CloseAccountReportController extends Controller
{
    public function index(Request $request)
    {

        $title = "Close Account Report";
        $searchFormLink = "closeAccountReport.index";
        $printFormLink = "closeAccountReport.print";
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;
        $type = $request->type;

        $closes = [];

        if ($request->searched) {
            $closes = InvoiceDuration::closeAccount($showroom_id, $project_id, $staffId, $type, $fromDate, $toDate);
        }

        return view('admin.closeAccountReport.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'closes', 'type','fromDate', 'toDate',));
    }


    public function print(Request $request)
    {
        $title = "Close Account Report";
// dd($request->all());
        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $type = $request->type;

        $staff = StaffSetup::where('id', $staffId)->first();


        $closes = InvoiceDuration::closeAccount($showroom_id, $project_id, $staffId, $type, $fromDate, $toDate);


        $pdf = PDF::loadView('admin.closeAccountReport.print', ['title' => $title, 'closes' => $closes, 'staff' => $staff, 'type' => $type], [],  ['format' => 'A4', 'orientation' => 'L']);

        return $pdf->stream('closeaccount.pdf');
    }
}
