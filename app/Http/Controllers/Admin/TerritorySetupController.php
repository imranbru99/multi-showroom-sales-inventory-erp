<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TerritorySetup;
use App\AreaSetup;

class TerritorySetupController extends Controller {

    public function index() {
        $title = "Territory Setup";

        if ($this->userRole == 1) {
            $territories = TerritorySetup::select('tbl_territories.*', 'tbl_area.name as areaName')
                    ->join('tbl_area', 'tbl_area.id', '=', 'tbl_territories.area_id')
                    ->orderBy('tbl_area.name', 'asc')
                    ->orderBy('tbl_territories.name', 'asc')
                    ->get();
        } else {
            $territories = TerritorySetup::select('tbl_territories.*', 'tbl_area.name as areaName')
                    ->join('tbl_area', 'tbl_area.id', '=', 'tbl_territories.area_id')
                    ->where('tbl_territories.company_id', $this->company)
//                    ->where('tbl_territories.showroom_id', $this->showroomId)
                    ->orderBy('tbl_area.name', 'asc')
                    ->orderBy('tbl_territories.name', 'asc')
                    ->get();
        }

        return view('admin.territorySetup.index')->with(compact('title', 'territories'));
    }

    public function add() {
        $title = "Add Territory";
        $formLink = "territorySetup.save";
        $buttonName = "Save";

        $allArea = AreaSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.territorySetup.add')->with(compact('title', 'formLink', 'buttonName', 'allArea'));
    }

    public function save(Request $request) {
        $this->validation($request);

        TerritorySetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'area_id' => $request->areaId,
            'code' => $request->code,
            'name' => $request->territoryName,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'created_by' => $this->userId
        ]);

        return redirect(route('territorySetup.index'))->with('msg', 'Territory Added Successfully');
    }

    public function edit($territoryId) {
        $title = "Edit Territory";
        $formLink = "territorySetup.update";
        $buttonName = "Update";

        $allArea = AreaSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();
        $territory = TerritorySetup::where('id', $territoryId)->first();

        return view('admin.territorySetup.edit')->with(compact('title', 'formLink', 'buttonName', 'territory', 'allArea'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $territoryId = $request->territoryId;

        $territory = TerritorySetup::find($territoryId);

        $territory->update([
            'area_id' => $request->areaId,
            'code' => $request->code,
            'name' => $request->territoryName,
            'incharge_name' => $request->inchargeName,
            'address' => $request->address,
            'contact' => $request->contact,
            'updated_by' => $this->userId
        ]);

        return redirect(route('territorySetup.index'))->with('msg', 'Territory Updated Successfully');
    }

    public function delete(Request $request) {
        TerritorySetup::where('id', $request->territoryId)->delete();
    }

    public function changeStatus(Request $request) {
        $territoryId = $request->territoryId;

        $territory = TerritorySetup::find($territoryId);

        if ($territory->status == 1) {
            $territory->update([
                'status' => 0
            ]);
        } else {
            $territory->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'areaId' => 'required',
            'code' => 'required',
            'territoryName' => 'required',
        ]);
    }

}
