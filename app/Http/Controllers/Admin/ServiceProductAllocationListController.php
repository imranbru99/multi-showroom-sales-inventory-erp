<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceAllocation;
use DB;
use PDF;

class ServiceProductAllocationListController extends Controller {

    public function index(Request $request) {
        $title = "Service Product Allocation";

        $searchFormLink = 'serviceProductAllocationList.index';
        $printFormLink = 'serviceProductAllocationList.print';

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $allocationLists = [];
        if ($request->print) {
            $allocationLists = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'remarks')
                    ->where('showroom_id', $this->showroomId)
                    ->whereBetween('issue_date', [$fromDate, $toDate])
                    ->where('status', '!=', 'Complete')
                    ->get();
        }

        return view('admin.serviceProductAllocationList.index')->with(compact('title', 'allocationLists', 'fromDate', 'toDate', 'print', 'searchFormLink', 'printFormLink'));
    }

    public function print(Request $request) {
        $title = "Service Product Allocation";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $allocationLists = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'remarks')
                    ->where('showroom_id', $this->showroomId)
                    ->whereBetween('issue_date', [$fromDate, $toDate])
                    ->where('status', '!=', 'Complete')
                    ->get();

        $pdf = PDF::loadView('admin.serviceProductAllocationList.print', ['allocationLists' => $allocationLists, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate]);

        return $pdf->stream('service_product_allocation_list_' . $fromDate . 'to' . $toDate . '.pdf');
    }

}
