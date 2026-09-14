<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AllowanceSetup;

class AllowanceSetupController extends Controller {

    public function index() {
        $title = "Allowance Setup";

        $allowances = AllowanceSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('title', 'asc')
                ->get();

        return view('admin.allowanceSetup.index')->with(compact('title', 'allowances'));
    }

    public function add() {
        $title = "Add Allowance";
        $formLink = "allowanceSetup.save";
        $buttonName = "Save";

        $allownaceTypes = array('Addition' => 'Addition', 'Subtraction' => 'Subtraction');

        return view('admin.allowanceSetup.add')->with(compact('title', 'formLink', 'buttonName', 'allownaceTypes'));
    }

    public function save(Request $request) {
//         dd($request->all());
        AllowanceSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'title' => $request->title,
            'code' => $request->code,
            'type' => $request->type,
            'remarks' => $request->remarks,
            'created_by' => $this->userId
        ]);

        return redirect(route('allowanceSetup.index'))->with('msg', 'Allowance Added Successfully');
    }

    public function edit($allowanceId) {
        $title = "Edit Allowance";
        $formLink = "allowanceSetup.update";
        $buttonName = "Update";

        $allownaceTypes = array('Addition' => 'Addition', 'Subtraction' => 'Subtraction');

        $allowance = AllowanceSetup::where('id', $allowanceId)->first();

        return view('admin.allowanceSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'allownaceTypes', 'allowance'));
    }

    public function update(Request $request) {
        $allowance = AllowanceSetup::find($request->allowanceId);

        $allowance->update([
            'title' => $request->title,
            'code' => $request->code,
            'type' => $request->type,
            'remarks' => $request->remarks,
            'created_by' => $this->userId
        ]);

        return redirect(route('allowanceSetup.index'))->with('msg', 'Allowance Added Successfully');
    }

    public function delete(Request $request) {
        AllowanceSetup::where('id', $request->allowanceId)->delete();
    }

    public function status(Request $request) {
        $allowance = AllowanceSetup::find($request->allowanceId);

        if ($allowance->status == 1) {
            $allowance->update([
                'status' => 0
            ]);
        } else {
            $allowance->update([
                'status' => 1
            ]);
        }
    }

}
