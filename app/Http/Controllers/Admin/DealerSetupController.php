<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use App\TerritorySetup;
use App\RegionSetup;
use App\AreaSetup;
use App\DealerDocument;
use App\StaffSetup;
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
        
        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->get();

        return view('admin.dealerSetup.add')->with(compact('title', 'formLink', 'buttonName', 'territories', 'districts', 'Areas', 'staffs'));
    }

    public function save(Request $request) {
        $this->validation($request);

        $dealer = DealerSetup::find($request->id);

        if (!$dealer) {
            DealerSetup::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'region_id' => $request->districtId,
                'area_id' => $request->upazilaId,
                'territory_id' => $request->territoryId,
                'type' => $request->dealerType,
                'code' => $request->code,
                // 'commission' => $request->commission,
                'name' => $request->dealerName,
                'short_name' => $request->short_name,
                'contact_person' => $request->contactPerson,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address' => $request->address,
                'courier_address' => $request->courier_address,
                'credit_limit' => $request->creditLimit,
//                'document' => $request->document,
                'reference_by' => $request->reference,
                'created_by' => $this->userId
            ]);
        } else {
            $dealer->update([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'region_id' => $request->districtId,
                'area_id' => $request->upazilaId,
                'territory_id' => $request->territoryId,
                'type' => $request->dealerType,
                'code' => $request->code,
                // 'commission' => $request->commission,
                'name' => $request->dealerName,
                'short_name' => $request->short_name,
                'contact_person' => $request->contactPerson,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address' => $request->address,
                'courier_address' => $request->courier_address,
                'credit_limit' => $request->creditLimit,
//                'document' => $request->document,
                'reference_by' => $request->reference,
                'created_by' => $this->userId
            ]);
        }

        return redirect(route('dealerSetup.index'))->with('msg', 'Dealer Added Successfully');
    }

    public function edit($dealerId) {
        $title = "Edit Dealer";
        $formLink = "dealerSetup.update";
        $buttonName = "Update";

        $dealer = DealerSetup::with('documents')->where('id', $dealerId)->first();

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

        $upazilas = AreaSetup::where('region_id', @$dealer->region_id)
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->get();


        return view('admin.dealerSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'territories', 'districts', 'upazilas', 'dealer', 'staffs'));
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
//            'document' => $request->document,
            'reference_by' => $request->reference,
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
                ->where('tbl_dealers.company_id', $this->company)
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                ->orderBy('tbl_dealers.name', 'asc')
                ->get();


        $pdf = PDF::loadView('admin.dealerSetup.print', ['dealers' => $dealers, 'title' => $title]);
        $pdf->stream('dealer_list.pdf');
    }

    //Document Section


    public function saveDocument(Request $request) {

        $dealer_id = $request->dealer_id;
        $dealer = DealerSetup::where('id', $dealer_id)->first();

        $document = "";
        if ($request->document) {
            $document = DealerDocument::create([
                        'dealer_id' => $dealer->id,
                        'title' => $request->title
            ]);
            $file = \App\HelperClass::fileUpload($request->document, 'tbl_dealer_documents', 'public/uploads/dealer_documents/');
            $document->update([
                'document' => $file,
            ]);
        }


        $doc = '
                    <tr id="doc_' . $document->id . '">
                                <td>' . $document->title . '</td>
                                <td>' . $document->document . '</td>
                                <td>
                                    <a href="' . route('dealerDocument.download', $document->id) . '" data-toggle="tooltip" data-original-title="Download"> <i class="fa fa-download m-r-10"></i> </a>
                                    <a href="' . route('dealerDocument.view', $document->id) . '" data-toggle="tooltip" data-original-title="View" target="_blank"> <i class="fa fa-eye text-success m-r-10"></i> </a>
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Delete" class="" onclick="removeDocument(' . $document->id . ')" style="width: 100%;"><i class="fa fa-trash text-danger"></i></a>
                                </td>
                            </tr>
            ';

        if ($request->ajax()) {
            return response()->json([
                        'document' => $doc
            ]);
        }
    }

    public function deleteDocument(Request $request) {
        $document = DealerDocument::find($request->id);
        try {
            if (!empty($document)) {
                @unlink($document->document);
                $document->delete();
                return response()->json(true);
            } else {
                return response()->json(false);
            }
        } catch (Exception $exc) {
            return response()->json(false);
        }
    }

    public function documentView($id) {
        $document = DealerDocument::find($id);
        $title = $document->title;

        $file = asset('public/uploads/dealer_documents/' . $document->document);


        return view('admin.dealerSetup.document_view')->with(compact('title', 'file'));
    }

    public function download($id) {
        $document = DealerDocument::find($id);
        return response()->download(public_path('/uploads/dealer_documents/' . $document->document));
    }

}
