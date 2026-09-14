<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\DesignationSetup;

class DesignationSetupController extends Controller
{
    public function index()
    {
    	$title = "Designation Setup";

    	$designations = DesignationSetup::where('showroom_id',$this->showroomId)
    		->orderBy('name','asc')
    		->get();

    	return view('admin.designationSetup.index')->with(compact('title','designations'));
    }

    public function add()
    {
    	$title = "Add Designation";
    	$formLink = "designationSetup.save";
    	$buttonName = "Save";

    	return view('admin.designationSetup.add')->with(compact('title','formLink','buttonName'));
    }

    public function save(Request $request)
    {
    	// dd($request->all());
        DesignationSetup::create([
            'showroom_id' => $this->showroomId,
        	'name' => $request->designationName,
        	'official_title' => $request->officialTitle,
        	'description' => $request->description,
            'created_by' => $this->userId
        ]);

        return redirect(route('designationSetup.index'))->with('msg','Designation Added Successfully');
    }

    public function edit($designationId)
    {
    	$title = "Edit Designation";
    	$formLink = "designationSetup.update";
    	$buttonName = "Update";

    	$designation = DesignationSetup::where('id',$designationId)->first();

    	return view('admin.designationSetup.edit')->with(compact('title','formLink','buttonName','designation'));
    }

    public function update(Request $request)
    {
    	$designation = DesignationSetup::find($request->designationId);

        $designation->update([
            'showroom_id' => $this->showroomId,
        	'name' => $request->designationName,
        	'official_title' => $request->officialTitle,
        	'description' => $request->description,
            'created_by' => $this->userId
        ]);

        return redirect(route('designationSetup.index'))->with('msg','Designation Updated Successfully');
    }

    public function delete(Request $request)
    {    	
        DesignationSetup::where('id',$request->designationId)->delete();
    }

    public function status(Request $request)
    {
        $designation = DesignationSetup::find($request->designationId);

        if ($designation->status == 1)
        {
            $designation->update([
                'status' => 0                
            ]);
        }
        else
        {
            $designation->update([
                'status' => 1                
            ]);
        }
    }
}
