<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\EmployeeSetup;
use App\DesignationSetup;

class EmployeeSetupController extends Controller
{
    public function index()
    {
    	$title = "Employee Setup";

    	$employies = EmployeeSetup::select('tbl_employee.*','tbl_showroom.name as showroomName','tbl_designation.name as designationName')
    		->leftJoin('tbl_showroom','tbl_showroom.id','=','tbl_employee.showroom_id')
    		->leftJoin('tbl_designation','tbl_designation.id','=','tbl_employee.designation_id')
    		->where('tbl_employee.showroom_id',$this->showroomId)
    		->orderBy('tbl_employee.name','asc')
    		->get();

    	return view('admin.employeeSetup.index')->with(compact('title','employies'));
    }

    public function add()
    {
    	$title = "Add Employee";
    	$formLink = "employeeSetup.save";
    	$buttonName = "Save";

    	$designations = DesignationSetup::orderBy('name','asc')->get();

    	return view('admin.employeeSetup.add')->with(compact('title','formLink','buttonName','designations'));
    }

    public function save(Request $request)
    {
    	// dd($request->all());
        if ($request->joiningDate == "")
        {
            $joiningDate = $request->joiningDate;
        }
        else
        {
            $joiningDate = date('Y-m-d', strtotime($request->joiningDate));
        }

        if (isset($request->image))
        {
            $employeeImage = \App\HelperClass::UploadImage($request->image,'tbl_employee','public/uploads/employee_image/');
        }
        else
        {
            $employeeImage = "";
        }

        EmployeeSetup::create([
            'showroom_id' => $this->showroomId,
            'designation_id' => $request->designation,
            'employee_no' => $request->employeeNo,
            'joining_date' => $joiningDate,
        	'name' => $request->employeeName,
        	'mobile' => $request->mobile,
        	'email' => $request->email,
        	'image' => $employeeImage,
            'created_by' => $this->userId
        ]);

        return redirect(route('employeeSetup.index'))->with('msg','Holiday Added Successfully');
    }

    public function edit($employeeId)
    {
    	$title = "Edit Employee";
    	$formLink = "employeeSetup.update";
    	$buttonName = "Update";

    	$designations = DesignationSetup::orderBy('name','asc')->get();
    	$employee = EmployeeSetup::where('id',$employeeId)->first();

    	return view('admin.employeeSetup.edit')->with(compact('title','formLink','buttonName','designations','employee'));
    }

    public function update(Request $request)
    {
    	// dd($request->all());
        if ($request->joiningDate == "")
        {
            $joiningDate = $request->joiningDate;
        }
        else
        {
            $joiningDate = date('Y-m-d', strtotime($request->joiningDate));
        }
        
        if ($request->resigningDate == "")
        {
            $resigningDate = $request->resigningDate;
        }
        else
        {
            $resigningDate = date('Y-m-d', strtotime($request->resigningDate));
        }

        if (isset($request->image))
        {
            if(file_exists($request->previousImage))
            {
                @unlink($request->previousImage);
            }
            $employeeImage = \App\HelperClass::UploadImage($request->image,'tbl_employee','public/uploads/employee_image/');
        }
        else
        {
            $employeeImage = $request->previousImage;
        }

    	$employee = EmployeeSetup::find($request->employeeId);

        $employee->update([
            'showroom_id' => $this->showroomId,
            'designation_id' => $request->designation,
            'employee_no' => $request->employeeNo,
            'joining_date' => $joiningDate,
            'resigning_date' => $resigningDate,
        	'name' => $request->employeeName,
        	'mobile' => $request->mobile,
        	'email' => $request->email,
        	'image' => $employeeImage,
        	'resigning_reason' => $request->resigningReason,
        	'resigned' => $request->resigned,
            'created_by' => $this->userId
        ]);

        return redirect(route('employeeSetup.index'))->with('msg','Holiday Added Successfully');
    }

    public function view($employeeId)
    {
    	$title = "View Employee";

    	$employee = EmployeeSetup::select('tbl_employee.*','tbl_showroom.name as showroomName','tbl_designation.name as designationName')
    		->leftJoin('tbl_showroom','tbl_showroom.id','=','tbl_employee.showroom_id')
    		->leftJoin('tbl_designation','tbl_designation.id','=','tbl_employee.designation_id')
    		->where('tbl_employee.showroom_id',$this->showroomId)
    		->where('tbl_employee.id',$employeeId)
    		->first();

    	return view('admin.employeeSetup.view')->with(compact('title','employee'));
    }

    public function delete(Request $request)
    {    	
        EmployeeSetup::where('id',$request->employeeId)->delete();
    }

    public function status(Request $request)
    {
        $employee = EmployeeSetup::find($request->employeeId);

        if ($employee->status == 1)
        {
            $employee->update([               
                'status' => 0                
            ]);
        }
        else
        {
            $employee->update([               
                'status' => 1                
            ]);
        }
    }
}
