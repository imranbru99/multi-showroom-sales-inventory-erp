<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AreaSetup;
use App\RegionSetup;

class AreaSetupController extends Controller {

    public function index() {
        $title = "Area Setup";
        if ($this->userRole == 1) {
            $allArea = AreaSetup::orderBy('name', 'asc')->get();
        } else {
            $allArea = AreaSetup::select('tbl_area.*', 'tbl_region.name as regionName')
                    ->join('tbl_region', 'tbl_region.id', '=', 'tbl_area.region_id')
                    ->where('tbl_area.company_id', $this->company)
//                    ->where('tbl_area.showroom_id', $this->showroomId)
                    ->orderBy('tbl_region.name', 'asc')
                    ->orderBy('tbl_area.name', 'asc')
                    ->get();
        }



        return view('admin.areaSetup.index')->with(compact('title', 'allArea'));
    }

    public function add() {
        $title = "Add Area";
        $formLink = "areaSetup.save";
        $buttonName = "Save";

        $allArea = RegionSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.areaSetup.add')->with(compact('title', 'formLink', 'buttonName', 'allArea'));

        // return view('admin.areaSetup.add')->with(compact('title','formLink','buttonName'));
    }

    public function save(Request $request) {
        $this->validation($request);

        AreaSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'region_id' => $request->regionId,
            'code' => $request->code,
            'name' => $request->areaName,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'email' => $request->email,
            'created_by' => $this->userId

                // 'code' => $request->code,
                //    'name' => $request->areaName,
                //    'incharge_name' => $request->inchargeName,
                //    'address' => $request->address,
                //    'contact' => $request->contact,           
                //    'email' => $request->email         
        ]);

        return redirect(route('areaSetup.index'))->with('msg', 'Area Added Successfully');
    }

    public function edit($areaId) {
        $title = "Edit Bank";
        $formLink = "areaSetup.update";
        $buttonName = "Update";

        $allRegion = RegionSetup::where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $area = AreaSetup::where('id', $areaId)->first();

        return view('admin.areaSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'area', 'allRegion'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $areaId = $request->areaId;

        $area = AreaSetup::find($areaId);

        $area->update([
            'showroom_id' => $this->showroomId,
            'area_id' => $request->regionId,
            'code' => $request->code,
            'name' => $request->areaName,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'email' => $request->email,
            'updated_by' => $this->userId

                // 'code' => $request->code,
                //    'name' => $request->areaName,
                //    'incharge_name' => $request->inchargeName,
                //    'address' => $request->address,
                //    'contact' => $request->contact,           
                //    'email' => $request->email         
        ]);

        return redirect(route('areaSetup.index'))->with('msg', 'Area Updated Successfully');
    }

    public function delete(Request $request) {
        AreaSetup::where('id', $request->areaId)->delete();
    }

    public function changeStatus(Request $request) {
        $areaId = $request->areaId;

        $area = AreaSetup::find($areaId);

        if ($area->status == 1) {
            $area->update([
                'status' => 0
            ]);
        } else {
            $area->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'regionId' => 'required',
            'code' => 'required',
            'areaName' => 'required',
        ]);
    }

}
