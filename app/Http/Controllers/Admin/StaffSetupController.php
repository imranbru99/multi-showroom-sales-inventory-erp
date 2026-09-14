<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\ShowroomSetup;
use PDF;
use MPDF;

class StaffSetupController extends Controller {

    public function index() {
        $title = "Staff Setup";

        $staffs = StaffSetup::with('company')
                ->where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.staffSetup.index')->with(compact('title', 'staffs'));
    }

    public function add() {
        $title = "Add Staff";
        $formLink = "staffSetup.save";
        $buttonName = "Save";
        
        $branches = ShowroomSetup::where('company_id', $this->company)->get();

        return view('admin.staffSetup.add')->with(compact('title', 'formLink', 'buttonName', 'branches'));
    }

    public function save(Request $request) {
        $this->validation($request);

        $joiningDate = Date('Y-m-d', strtotime($request->joiningDate));

        StaffSetup::create([
            'company_id' => $this->company,
            'showroom_id' => implode(',', $request->branch),
            'code' => $request->code,
            'designation' => $request->designation,
            'name' => $request->staffName,
            'short_name' => $request->short_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'email' => $request->email,
            'national_id' => $request->nationalId,
            'joining_date' => $joiningDate,
            'ac_no' => $request->ac_no,
            'ac_branch' => $request->ac_branch,
            'created_by' => $this->userId,
            'status' => 1
        ]);

        return redirect(route('staffSetup.index'))->with('msg', 'Staff Added Successfully');
    }

    public function edit($staffId) {
        $title = "Edit Staff";
        $formLink = "staffSetup.update";
        $buttonName = "Update";

        $staff = StaffSetup::where('id', $staffId)->first();
        $branches = ShowroomSetup::where('company_id', $this->company)->get();

        return view('admin.staffSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'staff', 'branches'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $joiningDate = Date('Y-m-d', strtotime($request->joiningDate));
        $staffId = $request->staffId;

        $staff = StaffSetup::find($staffId);

        $staff->update([
            'showroom_id' => implode(',', $request->branch),
            'code' => $request->code,
            'designation' => $request->designation,
            'name' => $request->staffName,
            'short_name' => $request->short_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'email' => $request->email,
            'national_id' => $request->nationalId,
            'joining_date' => $joiningDate,
            'ac_no' => $request->ac_no,
            'ac_branch' => $request->ac_branch,
            'updated_by' => $this->userId,
        ]);

        return redirect(route('staffSetup.index'))->with('msg', 'Staff Updated Successfully');
    }

    public function delete(Request $request) {
        StaffSetup::where('id', $request->staffId)->delete();
    }

    public function changeStatus(Request $request) {
        $staffId = $request->staffId;

        $staff = StaffSetup::find($staffId);

        if ($staff->status == 1) {
            $staff->update([
                'status' => 0
            ]);
        } else {
            $staff->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'code' => 'required',
            'staffName' => 'required',
            'branch' => 'required'
        ]);
    }

    public function print() {
        $title = "Staff Setup";

        $staffs = StaffSetup::where('company_id', @$this->company)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.staffSetup.print', ['title' => $title, 'staffs' => $staffs]);

        return $pdf->stream('staff_report.pdf');
    }

}
