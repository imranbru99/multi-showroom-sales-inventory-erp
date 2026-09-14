<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CompanySetup;
use App\StoreSetup;
use App\ShowroomSetup;

class ShowroomSetupController extends Controller {

    public function index(Request $request) {
        $title = "Branch Setup";
        if ($this->userRole == 1) {
            $showrooms = ShowroomSetup::with('company')->orderBy('name', 'asc')->get();
        } else {
            $showrooms = ShowroomSetup::with('company')
                    ->where('company_id', $this->company)
                    ->orderBy('name', 'asc')
                    ->get();
        }


        return view('admin.showroomSetup.index')->with(compact('title', 'showrooms'));
    }

    public function add() {
        $title = "Add New Branch";
        $formLink = "showroomSetup.save";
        $buttonName = "Save";

        if ($this->userRole == 1) {
            $companies = CompanySetup::where('status', 1)->get();
        } else {
            $companies = CompanySetup::where('id', $this->company)->first();
        }


        return view('admin.showroomSetup.add')->with(compact('title', 'formLink', 'buttonName', 'companies'));
    }

    public function save(Request $request) {

        $this->validate(request(), [
            'showroomName' => 'required',
            'prefix' => 'required',
        ]);


        if ($this->userRole == 1) {
            $company = $request->company;
        } else {
            $company = $this->company;
        }
        $showRoom = ShowroomSetup::create([
                    'company_id' => $company,
                    'prefix' => $request->prefix,
                    'name' => $request->showroomName,
                    'trade_license' => $request->tradeLicense,
                    'vat' => $request->vat,
                    'tin' => $request->tin,
                    'contact_person' => $request->contactPerson,
                    'email' => $request->email,
                    'website' => $request->webSite,
                    'phone' => $request->phoneNumber,
                    'fax' => $request->faxNumber,
                    'address' => $request->address
        ]);


        StoreSetup::create([
            'showroom_id' => $showRoom->id,
            'company_id' => $company,
            'code' => $request->code,
            'type' => 'Branch Store',
            'name' => $request->showroomName . ' Store',
            'address' => $request->address,
        ]);

        return redirect(route('showroomSetup.index'))->with('msg', 'Showroom Successfuly Saved');
    }

    public function edit($id) {
        $title = "Edit Branch";
        $formLink = "showroomSetup.update";
        $buttonName = "Update";
        $showroom = ShowroomSetup::where('id', $id)->first();

        if ($this->userRole == 1) {
            $companies = CompanySetup::where('status', 1)->get();
        } else {
            $companies = CompanySetup::where('id', $this->company)->first();
        }

        return view('admin.showroomSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'showroom', 'companies'));
    }

    public function update(Request $request) {

        $this->validate(request(), [
            'showroomName' => 'required',
            'prefix' => 'required',
        ]);

        $showroomId = $request->showroomId;
        $showroom = ShowroomSetup::find($showroomId);

        $showroom->update([
            'prefix' => $request->prefix,
            'name' => $request->showroomName,
            'trade_license' => $request->tradeLicense,
            'vat' => $request->vat,
            'tin' => $request->tin,
            'contact_person' => $request->contactPerson,
            'email' => $request->email,
            'website' => $request->webSite,
            'phone' => $request->phoneNumber,
            'fax' => $request->faxNumber,
            'address' => $request->address
        ]);

        return redirect(route('showroomSetup.index'))->with('msg', 'Showroom Successfuly Updated');
    }

    public function delete(Request $request) {
        ShowroomSetup::where('id', $request->showroomId)->delete();
    }

    public function changeStatus(Request $request) {
        $showroomId = $request->showroomId;

        $showroom = ShowroomSetup::find($showroomId);

        if ($showroom->status == 1) {
            $showroom->update([
                'status' => 0
            ]);
        } else {
            $showroom->update([
                'status' => 1
            ]);
        }
    }

}
