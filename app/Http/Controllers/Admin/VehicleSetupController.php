<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VehicleSetup;

class VehicleSetupController extends Controller {

    public function index() {
        $title = "Vehicle Setup";

        $vehicles = VehicleSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('type', 'asc')
                ->get();

        return view('admin.vehicleSetup.index')->with(compact('title', 'vehicles'));
    }

    public function add() {
        $title = "Add Vehicle";
        $formLink = "vehicleSetup.save";
        $buttonName = "Save";

        return view('admin.vehicleSetup.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request) {
        $this->validation($request);

        VehicleSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'registration_no' => $request->registrationNo,
            'type' => $request->type,
            'capacity' => $request->capacity,
        ]);

        return redirect(route('vehicleSetup.index'))->with('msg', 'Vehicle Added Successfully');
    }

    public function edit($vehicleId) {
        $title = "Edit Vehicle";
        $formLink = "vehicleSetup.update";
        $buttonName = "Update";

        $vehicle = VehicleSetup::where('id', $vehicleId)->first();

        return view('admin.vehicleSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'vehicle'));
    }

    public function update(Request $request) {
        $this->validation($request);

        $vehicleId = $request->vehicleId;

        $vehicle = VehicleSetup::find($vehicleId);

        $vehicle->update([
            'registration_no' => $request->registrationNo,
            'type' => $request->type,
            'capacity' => $request->capacity,
        ]);

        return redirect(route('vehicleSetup.index'))->with('msg', 'Vehicle Updated Successfully');
    }

    public function delete(Request $request) {
        VehicleSetup::where('id', $request->vehicleId)->delete();
    }

    public function changeStatus(Request $request) {
        $vehicleId = $request->vehicleId;

        $vehicle = VehicleSetup::find($vehicleId);

        if ($vehicle->status == 1) {
            $vehicle->update([
                'status' => 0
            ]);
        } else {
            $vehicle->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'registrationNo' => 'required',
        ]);
    }

}
