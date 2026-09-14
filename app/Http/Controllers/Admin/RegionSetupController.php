<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RegionSetup;
use App\TerritorySetup;
use App\AreaSetup;

class RegionSetupController extends Controller {

    public function index() {
        $title = "Region Setup";

        if ($this->userRole == 1) {
            $allRegion = RegionSetup::orderBy('name', 'asc')->get();
        } else {
            $allRegion = RegionSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->orderBy('name', 'asc')
                    ->get();
        }

        return view('admin.regionSetup.index')->with(compact('title', 'allRegion'));
    }

    public function add() {
        $title = "Add region";
        $formLink = "regionSetup.save";
        $buttonName = "Save";

        return view('admin.regionSetup.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request) {
        $this->validation($request);

        RegionSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'code' => $request->code,
            'name' => $request->name,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'email' => $request->email
        ]);

        return redirect(route('regionSetup.index'))->with('msg', 'Region Added Successfully');
    }

    public function edit($regionId) {
        $title = "Edit Region";
        $formLink = "regionSetup.update";
        $buttonName = "Update";

        $region = RegionSetup::where('id', $regionId)->first();

        return view('admin.regionSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'region'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $regionId = $request->regionId;

        $area = RegionSetup::find($regionId);

        $area->update([
            'code' => $request->code,
            'name' => $request->name,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'email' => $request->email
        ]);

        return redirect(route('regionSetup.index'))->with('msg', 'Region Updated Successfully');
    }

    public function delete(Request $request) {
        RegionSetup::where('id', $request->regionId)->delete();
    }

    public function changeStatus(Request $request) {
        $regionId = $request->regionId;

        $region = RegionSetup::find($regionId);

        if ($region->status == 1) {
            $region->update([
                'status' => 0
            ]);
        } else {
            $region->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'code' => 'required',
            'name' => 'required|unique:tbl_region',
        ]);
    }

    public function areaRegionTerritory(Request $request) {
        if ($request->distribution == 'region') {
            $location = RegionSetup::where('company_id', $this->company)->get();
        } elseif ($request->distribution == 'area') {
            $location = AreaSetup::where('company_id', $this->company)->get();
        } elseif ($request->distribution == 'territory') {
            $location = TerritorySetup::where('company_id', $this->company)->get();
        }

        return $location;
    }

}
