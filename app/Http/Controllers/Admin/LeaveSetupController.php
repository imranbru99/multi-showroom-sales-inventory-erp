<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\LeaveSetup;

class LeaveSetupController extends Controller
{
    public function index()
    {
    	$title = "Leave Setup";

    	$leaves = LeaveSetup::where('showroom_id',$this->showroomId)
    		->orderBy('name','asc')
    		->get();

    	return view('admin.leaveSetup.index')->with(compact('title','leaves'));
    }

    public function add()
    {
    	$title = "Add Leave";
    	$formLink = "leaveSetup.save";
    	$buttonName = "Save";

    	$leaveTypes = array('Pay'=>'Pay','Non-Pay'=>'Non-Pay');

    	return view('admin.leaveSetup.add')->with(compact('title','formLink','buttonName','leaveTypes'));
    }

    public function save(Request $request)
    {
    	// dd($request->all());
        LeaveSetup::create( [
            'showroom_id' => $this->showroomId,
        	'name' => $request->leaveName,
        	'type' => $request->leaveType,
            'days' => $request->numberOfDay,
            'created_by' => $this->userId
        ]);

        return redirect(route('leaveSetup.index'))->with('msg','Leave Added Successfully');
    }

    public function edit($leaveId)
    {
    	$title = "Edit Leave";
    	$formLink = "leaveSetup.update";
    	$buttonName = "Update";

    	$leaveTypes = array('Pay'=>'Pay','Non-Pay'=>'Non-Pay');
    	$leave = LeaveSetup::where('id',$leaveId)->first();

    	return view('admin.leaveSetup.edit')->with(compact('title','formLink','buttonName','leaveTypes','leave'));
    }

    public function update(Request $request)
    {
    	$leave = LeaveSetup::find($request->leaveId);

        $leave->update([
            'showroom_id' => $this->showroomId,
        	'name' => $request->leaveName,
        	'type' => $request->leaveType,
            'days' => $request->numberOfDay,
            'created_by' => $this->userId
        ]);

        return redirect(route('leaveSetup.index'))->with('msg','Leave Updated Successfully');
    }

    public function delete(Request $request)
    {    	
        LeaveSetup::where('id',$request->leaveId)->delete();
    }

    public function status(Request $request)
    {
        $leave = LeaveSetup::find($request->leaveId);

        if ($leave->status == 1)
        {
            $leave->update( [               
                'status' => 0                
            ]);
        }
        else
        {
            $leave->update( [               
                'status' => 1                
            ]);
        }
    }
}
