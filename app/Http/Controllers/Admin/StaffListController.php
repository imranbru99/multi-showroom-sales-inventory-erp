<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\ShowroomSetup;
use DB;
use PDF;
use MPDF;

class StaffListController extends Controller {

    public function index(Request $request) {
        $title = "Staff List";
        $searchFormLink = "staffList.index";
        $printFormLink = "staffList.print";

        $branch = $request->branch;
        $print = $request->print;


        $branches = ShowroomSetup::where('company_id', $this->company)->get();

        if ($branch) {
            $id = [];
            $count = count($branch);
            for ($i = 0; $i < $count; $i++) {
                $staffs = StaffSetup::select('id')
                        ->where('company_id', $this->company)
                        ->whereRaw('FIND_IN_SET(showroom_id, ' . $branch[$i] . ')')
                        ->get();

                foreach ($staffs as $staff) {
                    $id[] = $staff->id;
                }
            }

            $staffs = StaffSetup::where('company_id', $this->company)
                    ->whereIn('id', $id)
                    ->orderBy('name', 'asc')
                    ->get();
        } else {
            $staffs = StaffSetup::where('company_id', $this->company)->orderBy('name', 'asc')->get();
        }

        return view('admin.staffList.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'branches', 'branch', 'print', 'staffs'));
    }

    public function print(Request $request) {
        $title = "Staff List";

        $branch = $request->branch;
        $print = $request->print;

        if ($branch) {
            $id = [];
            $count = count($branch);
            for ($i = 0; $i < $count; $i++) {
                $staffs = StaffSetup::select('id')
                        ->where('company_id', $this->company)
                        ->whereRaw('FIND_IN_SET(showroom_id, ' . $branch[$i] . ')')
                        ->get();

                foreach ($staffs as $staff) {
                    $id[] = $staff->id;
                }
            }

            $staffs = StaffSetup::where('company_id', $this->company)
                    ->whereIn('id', $id)
                    ->orderBy('name', 'asc')
                    ->get();
        } else {
            $staffs = StaffSetup::where('company_id', $this->company)->orderBy('name', 'asc')->get();
        }

        $pdf = PDF::loadView('admin.staffList.print', compact('title', 'staffs'));

        return $pdf->stream('staff_list.pdf');
    }

}
