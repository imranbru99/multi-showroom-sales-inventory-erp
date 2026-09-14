<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShowroomProjectSetup;

class ShowroomProjectController extends Controller {

    public function index() {
        $title = "Showroom Project Setup";

        $showroomProjects = ShowroomProjectSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.showroomProjectSetup.index')->with(compact('title', 'showroomProjects'));
    }

    public function add(Request $request) {
        $title = "Add Showroom Project";
        $formLink = "showroomProjectSetup.save";
        $buttonName = "Save";

        return view('admin.showroomProjectSetup.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request) {
        $this->validation($request);

        ShowroomProjectSetup::create([
            'company_id' => $this->company,
            'code' => $request->code,
            'showroom_id' => $this->showroomId,
            'name' => $request->showroomProjectName,
            'address' => $request->address,
            'remarks' => $request->remarks
        ]);

        return redirect(route('showroomProjectSetup.index'))->with('msg', 'Showroom project Added Successfully');
    }

    public function edit($showroomProjectId) {
        $title = "Edit Showroom Project";
        $formLink = "showroomProjectSetup.update";
        $buttonName = "Update";

        $ShowroomProject = ShowroomProjectSetup::where('id', $showroomProjectId)->first();

        return view('admin.showroomProjectSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'ShowroomProject'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $showroomProjectId = $request->showroomProjectId;

        $ShowroomProject = ShowroomProjectSetup::find($showroomProjectId);

        $ShowroomProject->update([
            'code' => $request->code,
            'name' => $request->showroomProjectName,
            'address' => $request->address,
            'remarks' => $request->remarks,
        ]);

        return redirect(route('showroomProjectSetup.index'))->with('msg', 'Showroom Project Updated Successfully');
    }

    public function delete(Request $request) {
        ShowroomProjectSetup::where('id', $request->showroomProjectId)->delete();
    }

    public function changeStatus(Request $request) {
        $showroomProjectId = $request->showroomProjectId;

        $ShowroomProject = ShowroomProjectSetup::find($showroomProjectId);

        if ($ShowroomProject->status == 1) {
            $ShowroomProject->update([
                'status' => 0
            ]);
        } else {
            $ShowroomProject->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'code' => 'required',
            'showroomProjectName' => 'required'
        ]);
    }

}
