<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveSetup;
use App\LeaveRequest;
use App\StaffSetup;
use DateTime;

class LeaveRequestController extends Controller {

    public function index() {
        $title = "Leave Request";

        $leaverequests = LeaveRequest::select('tbl_leave_requests.*', 'tbl_leaves.name as leaveType', 'tbl_staffs.name as staffName')
                ->leftjoin('tbl_staffs', 'tbl_staffs.id', 'tbl_leave_requests.employee_id')
                ->leftjoin('tbl_leaves', 'tbl_leaves.id', 'tbl_leave_requests.leave_type')
                ->where('tbl_leave_requests.company_id', $this->company)
//                ->where('tbl_leave_requests.showroom_id', $this->showroomId)
                ->orderBy('tbl_leave_requests.id', 'desc')
                ->get();

        return view('admin.leaveRequest.index')->with(compact('title', 'leaverequests'));
    }

    public function add() {
        $title = "Add Leave Request";
        $formLink = "leaveRequest.save";
        $buttonName = "Request";

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->cursor();
        $leaveTypes = LeaveSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->cursor();


        return view('admin.leaveRequest.add')->with(compact('title', 'formLink', 'buttonName', 'staffs', 'leaveTypes'));
    }

    public function leaveInfo(Request $request) {
        $leaveTypes = LeaveSetup::where('id', $request->leave_type)->first();
        $staff = StaffSetup::where('id', $request->employee_id)->first();
        $leaveDuration = LeaveRequest::where('employee_id', $request->employee_id)->sum('duration');

        $dueDuration = $leaveTypes->days - $leaveDuration;

        return $dueDuration;
    }

    public function save(Request $request) {
        $from = date('Y-m-d', strtotime($request->leave_from));
        $to = date('Y-m-d', strtotime($request->leave_to));

        LeaveRequest::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'employee_id' => $request->employee_id,
            'leave_type' => $request->leaveType,
            'leave_from' => $from,
            'leave_to' => $to,
            'duration' => $request->leave_day,
            'leave_day' => $request->leave_day,
            'remarks' => $request->remarks,
            'created_by' => $this->userId
        ]);

        return redirect(route('leaveRequest.index'))->with('msg', 'Leave Added Successfully');
    }

    public function edit($leaveReqId) {
        $title = "Edit Leave";
        $formLink = "leaveRequest.update";
        $buttonName = "Update";


        $staffs = StaffSetup::where('status', 1)->cursor();
        $leaveTypes = LeaveSetup::cursor();
        $leaveRequest = LeaveRequest::where('id', $leaveReqId)->first();


        //calculate due leave
        $ltype = LeaveSetup::where('id', $leaveRequest->leave_type)->first();
        $leaveDuration = LeaveRequest::where('employee_id', $leaveRequest->employee_id)->sum('duration');
        $dueDuration = $ltype->days - $leaveDuration;



        return view('admin.leaveRequest.edit')->with(compact('title', 'formLink', 'buttonName', 'leaveRequest', 'leaveTypes', 'staffs', 'dueDuration'));
    }

    public function update(Request $request) {
        $leaveRequest = LeaveRequest::find($request->leaveReqId);

        $from = date('Y-m-d', strtotime($request->leave_from));
        $to = date('Y-m-d', strtotime($request->leave_to));


        $leaveRequest->update([
            'employee_id' => $request->employee_id,
            'leave_type' => $request->leaveType,
            'leave_from' => $from,
            'leave_to' => $to,
            'duration' => $request->leave_day,
            'leave_day' => $request->leave_day,
            'remarks' => $request->remarks,
            'updated_by' => $this->userId
        ]);

        return redirect(route('leaveRequest.index'))->with('msg', 'Leave Request Updated Successfully');
    }

    public function delete(Request $request) {
        LeaveRequest::where('id', $request->leaveReqId)->delete();
    }

}
