<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveTypeSetup;

class LeaveTypeController extends Controller {

    public function index() {
        $title = "Leave Setup";

        $leaves = LeaveTypeSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.leaveType.index')->with(compact('title', 'leaves'));
    }

    public function add() {
        $title = "Add Leave";
        $formLink = "leaveType.save";
        $buttonName = "Save";



        return view('admin.leaveType.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request) {
        // dd($request->all());
        LeaveTypeSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'name' => $request->leaveName,
            'days' => $request->numberOfDay,
            'created_by' => $this->userId
        ]);

        return redirect(route('leaveType.index'))->with('msg', 'Leave Added Successfully');
    }

    public function edit($leaveId) {
        $title = "Edit Leave";
        $formLink = "leaveType.update";
        $buttonName = "Update";

        $leave = LeaveTypeSetup::where('id', $leaveId)->first();

        return view('admin.leaveType.edit')->with(compact('title', 'formLink', 'buttonName', 'leave'));
    }

    public function update(Request $request) {
        $leave = LeaveTypeSetup::find($request->leaveId);

        $leave->update([
            'name' => $request->leaveName,
            'days' => $request->numberOfDay,
            'created_by' => $this->userId
        ]);

        return redirect(route('leaveType.index'))->with('msg', 'Leave Updated Successfully');
    }

    public function delete(Request $request) {
        LeaveTypeSetup::where('id', $request->leaveId)->delete();
    }

    public function status(Request $request) {
        $leave = LeaveTypeSetup::find($request->leaveId);

        if ($leave->status == 1) {
            $leave->update([
                'status' => 0
            ]);
        } else {
            $leave->update([
                'status' => 1
            ]);
        }
    }

}
