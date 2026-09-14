<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\EmployeeCommission;
use App\StaffSetup;

class EmployeeCommissionController extends Controller
{
    public function index()
    {
    	$title = "Employee Commission";

    	$commissions = EmployeeCommission::with('staff', 'user')->where('showroom_id', @$this->showroomId)->orderBy('id','desc')->get();

    	return view('admin.employeeCommission.index')->with(compact('title','commissions'));
    }

    public function add()
    {
    	$title = "Add Employee Commission";
    	$formLink = "employeeCommission.save";
    	$buttonName = "Save";

        $employees = StaffSetup::where('showroom_id', @$this->showroomId)->orderBy('name', 'asc')->get();

    	return view('admin.employeeCommission.add')->with(compact('title','formLink','buttonName', 'employees'));
    }

    public function save(Request $request)
    {

        EmployeeCommission::create( [
            'showroom_id' => $this->showroomId,
        	'employee_id' => $request->employee_id,
        	'commission' => $request->commission,
            'created_by' => $this->userId,           
        ]);

        return redirect(route('employeeCommission.index'))->with('msg','Commission Added Successfully');
    }

    public function edit($id)
    {
    	$title = "Edit Bank Loan";
    	$formLink = "employeeCommission.update";
    	$buttonName = "Update";

        $employees = StaffSetup::where('showroom_id', @$this->showroomId)->orderBy('name', 'asc')->get();

    	$commission = EmployeeCommission::where('id',$id)->first();

    	return view('admin.employeeCommission.edit')->with(compact('title','formLink','buttonName','commission', 'employees'));
    }

    public function update(Request $request)
    {

        $commission = EmployeeCommission::find($request->id);

        $commission->update( [
            'showroom_id' => $this->showroomId,
        	'employee_id' => $request->employee_id,
        	'commission' => $request->commission,
            'created_by' => $this->userId,          
        ]);

        return redirect(route('employeeCommission.index'))->with('msg','Commission Updated Successfully');
    }

    public function delete(Request $request)
    {    	
        EmployeeCommission::where('id',$request->id)->delete();
    }

}
