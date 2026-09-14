<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveSetup;
use App\LeaveRequest;
use App\StaffSetup;
use DateTime;

class LeaveRequestApproveController extends Controller {

    public function index() {
        $title = "Leave Requests Approval";

        $leaverequests = LeaveRequest::select('tbl_leave_requests.*', 'tbl_leaves.name as leaveType', 'tbl_staffs.name as staffName')
                ->leftjoin('tbl_staffs', 'tbl_staffs.id', 'tbl_leave_requests.employee_id')
                ->leftjoin('tbl_leaves', 'tbl_leaves.id', 'tbl_leave_requests.leave_type')
                ->where('tbl_leave_requests.company_id', $this->company)
                ->where('tbl_leave_requests.showroom_id', $this->showroomId)
                ->whereNotNull('approve_by')
                ->whereNull('approve_by_a')
                ->orderBy('tbl_leave_requests.id', 'desc')
                ->get();

        $leaverequests_suggests = LeaveRequest::select('tbl_leave_requests.*', 'tbl_leaves.name as leaveType', 'tbl_staffs.name as staffName')
                ->leftjoin('tbl_staffs', 'tbl_staffs.id', 'tbl_leave_requests.employee_id')
                ->leftjoin('tbl_leaves', 'tbl_leaves.id', 'tbl_leave_requests.leave_type')
                ->where('tbl_leave_requests.company_id', $this->company)
                ->where('tbl_leave_requests.showroom_id', $this->showroomId)
                ->whereNotNull('approve_by_a')
                ->orderBy('tbl_leave_requests.id', 'desc')
                ->get();

        return view('admin.leaveApprove.index')->with(compact('title', 'leaverequests', 'leaverequests_suggests'));
    }

    public function getApproveInfo(Request $request) {
        $leaveRequest = LeaveRequest::select('tbl_leave_requests.*',
                        'tbl_leaves.name as leaveType',
                        'tbl_staffs.code',
                        'tbl_staffs.name as staffName')
                ->leftjoin('tbl_staffs', 'tbl_staffs.id', 'tbl_leave_requests.employee_id')
                ->leftjoin('tbl_leaves', 'tbl_leaves.id', 'tbl_leave_requests.leave_type')
                ->where('tbl_leave_requests.id', $request->id)
                ->first();

        $leaveInfo = [
            'id' => $leaveRequest->id,
            'employee_id' => $leaveRequest->employee_id,
            'leave_type' => $leaveRequest->leave_type,
            'leave_from' => date('d-m-Y', strtotime($leaveRequest->leave_from)),
            'leave_to' => date('d-m-Y', strtotime($leaveRequest->leave_to)),
            'duration' => $leaveRequest->duration,
            'leave_day' => $leaveRequest->leave_day,
            'leaveType' => $leaveRequest->leaveType,
            'code' => $leaveRequest->code,
            'staffName' => $leaveRequest->staffName,
            'leave_from_a' => $leaveRequest->leave_from_a,
            'leave_to_a' => $leaveRequest->leave_to_a,
        ];


        //calculate due leave
        $ltype = LeaveSetup::where('id', $leaveRequest->leave_type)->first();
        $leaveDuration = LeaveRequest::where('employee_id', $leaveRequest->employee_id)->sum('duration');
        $dueDuration = $ltype->days - $leaveDuration;

        $data = [
            'leaveRequest' => $leaveInfo,
            'dueDuration' => $dueDuration
        ];


        return $data;
    }

    public function save(Request $request) {
        $from = date('Y-m-d', strtotime($request->leave_from_a));
        $to = date('Y-m-d', strtotime($request->leave_to_a));


        $leaveReq = LeaveRequest::find($request->id);
        $leaveReq->update([
            'leave_from_a' => $from,
            'leave_to_a' => $to,
            'duration' => $request->leave_day,
            'leave_day' => $request->leave_day,
            'remarks' => $request->remarks,
            'approve_by_a' => $this->userId
        ]);

        return redirect()->back()->with('msg', 'Leave Approve Successfully');
    }

    public function approve(Request $request) {


        $from = date('Y-m-d', strtotime($request->leave_from_a));
        $to = date('Y-m-d', strtotime($request->leave_to_a));


        $leaveReq = LeaveRequest::find($request->id);
        $leaveReq->update([
            'leave_from_a' => $from,
            'leave_to_a' => $to,
            'duration' => $request->leave_day,
            'leave_day' => $request->leave_day,
            'remarks' => $request->remarks,
            'approve_by_a' => $this->userId
        ]);

        return redirect()->back()->with('msg', 'Leave Approve Successfully');
    }

}
