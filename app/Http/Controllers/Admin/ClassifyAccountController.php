<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\ShowroomProjectSetup;
use App\StaffSetup;
use App\InstallmentCollectionList;
use Illuminate\Support\Carbon;
use App\Services\RetailSale\Report\InvoiceDuration;
use PDF;

class ClassifyAccountController extends Controller
{

    public function index(Request $request)
    {
        $title = "Classify Account";
        $searchFormLink = "classifyAccount.index";
        $printFormLink = "classifyAccount.print";

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();


        $print = $request->print;

        $project_id = $request->project;
        $sales_by = $request->sales_by;

        $showroom_id = $this->showroomId;


        $fromDate = date('Y-m-d', strtotime(now()->subDays(90)));
        $toDate = date('Y-m-d');

        $staffs = StaffSetup::where('status', 1)->get();

        $customers = [];

        if ($request->project) {
            $customers = InvoiceDuration::classifyAccount($sales_by, $showroom_id, $project_id);
        }

        return view('admin.classifyAccounts.index')->with(compact('title', 'project_id', 'projects', 'searchFormLink', 'printFormLink', 'print', 'customers', 'staffs', 'sales_by'));
    }

    public function print(Request $request)
    {
        $title = "Classify Account";

        $sales_by = $request->sales_by;
        $project_id = $request->project;

        $staff = StaffSetup::find(@$sales_by);

        $showroom_id = $this->showroomId;

        $staffs = StaffSetup::where('id', $sales_by)->first();

        $fromDate = date('Y-m-d', strtotime(now()->subDays(90)));
        $toDate = date('Y-m-d');

        $customers = [];

        if ($request->project) {
            $customers = InvoiceDuration::classifyAccount($sales_by, $showroom_id, $project_id);
        }


        $pdf = PDF::loadView('admin.classifyAccounts.print', ['title' => $title, 'customers' => $customers, 'sales_by' => $sales_by, 'staff' => $staff]);

        return $pdf->stream('classify_accounts.pdf');
    }
}
