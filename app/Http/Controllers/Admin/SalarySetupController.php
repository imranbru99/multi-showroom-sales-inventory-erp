<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalarySetup;
use PDF;
use MPDF;

class SalarySetupController extends Controller {

    public function index() {
        $title = "Salary Setup";

        $salarySetups = SalarySetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        return view('admin.salarySetup.index')->with(compact('title', 'salarySetups'));
    }

    public function add() {
        $title = "Add Salary Setup";
        $formLink = "salarySetup.save";
        $buttonName = "Save";

        return view('admin.salarySetup.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request) {

        SalarySetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'title' => $request->title,
            'cut_on' => $request->cut_on,
            'salary_cut' => $request->salary_cut,
            'status' => 1,
        ]);

        return redirect(route('salarySetup.index'))->with('msg', 'Salary Setup Added Successfully');
    }

    public function edit($salarySetupId) {
        $title = "Edit Salary Setup";
        $formLink = "salarySetup.update";
        $buttonName = "Update";

        $salarySetup = SalarySetup::where('id', $salarySetupId)->first();

        return view('admin.salarySetup.edit')->with(compact('title', 'formLink', 'buttonName', 'salarySetup'));
    }

    public function update(Request $request) {
        $salarySetupId = $request->salarySetupId;

        $salarySetup = SalarySetup::find($salarySetupId);

        $salarySetup->update([
            'title' => $request->title,
            'cut_on' => $request->cut_on,
            'salary_cut' => $request->salary_cut,
        ]);

        return redirect(route('salarySetup.index'))->with('msg', 'Salary Setup Updated Successfully');
    }

    public function delete(Request $request) {
        SalarySetup::where('id', $request->salarySetupId)->delete();
    }

    public function status(Request $request) {
        $salarySetupId = $request->salarySetupId;

        $salarySetup = SalarySetup::find($salarySetupId);

        if ($salarySetup->status == 1) {
            $salarySetup->update([
                'status' => 0
            ]);
        } else {
            $salarySetup->update([
                'status' => 1
            ]);
        }
    }

}
