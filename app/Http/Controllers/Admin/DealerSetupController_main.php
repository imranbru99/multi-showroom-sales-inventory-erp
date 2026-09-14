<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use App\TerritorySetup;
use App\RegionSetup;
use App\AreaSetup;
use PDF;

class DealerSetupController extends Controller {

    public function index() {
        $title = "Dealer Setup";

        if ($this->userRole == 1) {
            $dealers = DealerSetup::select('tbl_dealers.*', 'tbl_region.name as regionName', 'tbl_area.name as areaName', 'tbl_territories.name as territoryName')
                    ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                    ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                    ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                    ->orderBy('tbl_region.name', 'asc')
                    ->orderBy('tbl_area.name', 'asc')
                    ->orderBy('tbl_dealers.name', 'asc')
                    ->get();
        } else {
            $dealers = DealerSetup::select('tbl_dealers.*', 'tbl_region.name as regionName', 'tbl_area.name as areaName', 'tbl_territories.name as territoryName')
                    ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                    ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                    ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                    ->where('tbl_dealers.company_id', $this->company)
//                    ->where('tbl_dealers.showroom_id', $this->showroomId)
                    ->orderBy('tbl_region.name', 'asc')
                    ->orderBy('tbl_area.name', 'asc')
                    ->orderBy('tbl_dealers.name', 'asc')
                    ->get();
        }

        return view('admin.dealerSetup.index')->with(compact('title', 'dealers'));
    }

    public function add() {
        $title = "Add Dealer";
        $formLink = "dealerSetup.save";
        $buttonName = "Save";

        $territories = TerritorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $districts = RegionSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $Areas = AreaSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.dealerSetup.add')->with(compact('title', 'formLink', 'buttonName', 'territories', 'districts', 'Areas'));
    }

    public function save(Request $request) {
        $this->validation($request);

        DealerSetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'region_id' => $request->districtId,
            'area_id' => $request->upazilaId,
            'territory_id' => $request->territoryId,
            'type' => $request->dealerType,
            'code' => $request->code,
            'commission' => $request->commission,
            'name' => $request->dealerName,
            'short_name' => $request->short_name,
            'contact_person' => $request->contactPerson,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'courier_address' => $request->courier_address,
            'credit_limit' => $request->creditLimit,
            'created_by' => $this->userId
        ]);

        return redirect(route('dealerSetup.index'))->with('msg', 'Dealer Added Successfully');
    }

    public function edit($dealerId) {
        $title = "Edit Dealer";
        $formLink = "dealerSetup.update";
        $buttonName = "Update";

        $dealer = DealerSetup::where('id', $dealerId)->first();

        $territories = TerritorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();
        
        $districts = RegionSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();
        
        $upazilas = AreaSetup::where('region_id', $dealer->region_id)
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.dealerSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'territories', 'districts', 'upazilas', 'dealer'));
    }

    public function update(Request $request) {
        // dd($request->all());
        $this->validation($request);

        $dealerId = $request->dealerId;

        $dealer = DealerSetup::find($dealerId);

        $dealer->update([
            'region_id' => $request->districtId,
            'area_id' => $request->upazilaId,
            'territory_id' => $request->territoryId,
            'type' => $request->dealerType,
            'code' => $request->code,
            'commission' => $request->commission,
            'name' => $request->dealerName,
            'short_name' => $request->short_name,
            'contact_person' => $request->contactPerson,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'courier_address' => $request->courier_address,
            'credit_limit' => $request->creditLimit,
            'updated_by' => $this->userId
        ]);

        return redirect(route('dealerSetup.index'))->with('msg', 'Dealer Updated Successfully');
    }

    public function getAllUpazilaByDistrict(Request $request) {
        $output = '';

        $upazilas = AreaSetup::where('region_id', $request->districtId)->where('status', '1')->get();

        if ($upazilas) {
            $output .= '<select class="form-control chosen-select" name="upazilaId" id="upazilaId">';
            $output .= '<option value="">Select Upazila</option>';
            foreach ($upazilas as $upazila) {
                $output .= '<option value="' . $upazila->id . '">' . $upazila->name . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="form-control chosen-select" name="upazilaId" id="upazilaId">';
            $output .= '<option value="">Select Upazila</option>';
            $output .= '</select>';
        }

        echo $output;
        $output = '';
    }

    public function getAllTeritoryByArea(Request $request) {
        $territories = TerritorySetup::where('area_id', $request->upazilaId)->where('status', '1')->get();

        $output = '';
        if ($territories) {
            $output .= '<select class="form-control chosen-select" name="territoryId" id="territoryId">';
            $output .= '<option value="">Select territories</option>';
            foreach ($territories as $territorie) {
                $output .= '<option value="' . $territorie->id . '">' . $territorie->name . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="form-control chosen-select" name="territoryId" id="territoryId">';
            $output .= '<option value="">Select territories</option>';
            $output .= '</select>';
        }

        echo $output;
        $output = '';
    }

    public function delete(Request $request) {
        DealerSetup::where('id', $request->dealerId)->delete();
    }

    public function changeStatus(Request $request) {
        $dealer = DealerSetup::find($request->dealerId);

        if ($dealer->status == 1) {
            $dealer->update([
                'status' => 0
            ]);
        } else {
            $dealer->update([
                'status' => 1
            ]);
        }
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'dealerName' => 'required',
        ]);
    }

    public function print() {
        $title = "Dealer List";

        $dealers = DealerSetup::select(
                        'tbl_dealers.*',
                        'tbl_region.name as regionName',
                        'tbl_area.name as areaName',
                        'tbl_territories.name as territoryName'
                )
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                ->orderBy('tbl_dealers.name', 'asc')
                ->get();


        $pdf = PDF::loadView('admin.dealerSetup.print', ['dealers' => $dealers, 'title' => $title]);
        $pdf->stream('dealer_list.pdf');
    }

}
