<?php

namespace App\Http\Controllers\admin;

use PDF;
use App\CloseAccount;
use App\StaffSetup;
use App\ShowroomProjectSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RetailSale\Report\InvoiceDuration;

class MissingAmountReportController extends Controller
{
    public function index(Request $request)
    {

        $title = "Missing Amount Report";
        $searchFormLink = "missingAmountReport.index";
        $printFormLink = "missingAmountReport.print";

        $showroom_id = @$this->showroomId;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $staffs = StaffSetup::where('status', 1)->get();

        $project_id = $request->project;
        $staffId = $request->staff;

        $missings = [];

        if ($request->searched) {
            $missings = InvoiceDuration::missingAmount($showroom_id, $project_id, $staffId);
        }

        return view('admin.missingAmount.index')->with(compact('project_id', 'staffs', 'staffId', 'projects', 'title', 'searchFormLink', 'printFormLink', 'missings'));
    }


    public function print(Request $request)
    {
        $title = "Missing Amount Report";

        $showroom_id = @$this->showroomId;

        $project_id = $request->project;
        $staffId = $request->staff;

        $staff = StaffSetup::where('id', $staffId)->first();


        $missings = InvoiceDuration::missingAmount($showroom_id, $project_id, $staffId);


        $pdf = PDF::loadView('admin.missingAmount.print', ['title' => $title, 'missings' => $missings, 'staff' => $staff]);

        return $pdf->stream('missing_amount.pdf');
    }
}
