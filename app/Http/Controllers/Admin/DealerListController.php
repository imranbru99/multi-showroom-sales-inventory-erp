<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerSetup;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use App\StaffSetup;
use DB;
use PDF;
use MPDF;

class DealerListController extends Controller {

    public function index(Request $request) {
        $title = "Dealer List";
        $searchFormLink = "dealerList.index";
        $printFormLink = "dealerList.print";

        $print = $request->print;
        $reference = $request->reference;

        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;

        $areas = [];
        $territories = [];
        if ($region) {
            $areas = AreaSetup::where('region_id', $region)->get();
        }

        if ($area) {
            $territories = TerritorySetup::where('area_id', $area)->get();
        }


        $regions = RegionSetup::where('company_id', $this->company)->get();
        $staffs = StaffSetup::where('company_id', $this->company)->get();

        $dealers = DealerSetup::where('company_id', $this->company)
                ->where('status', 1)
                ->where(function($query) use($region, $area, $territory, $reference) {
                    if ($region) {
                        $query->where('region_id', $region);
                    }
                    if ($area) {
                        $query->where('area_id', $area);
                    }
                    if ($territory) {
                        $query->where('territory_id', $territory);
                    }
                    if ($reference) {
                        $query->where('reference_by', $reference);
                    }
                })
                ->get();

        return view('admin.dealerList.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'region', 'area', 'territory', 'reference', 'staffs', 'regions', 'areas', 'territories', 'dealers'));
    }

    public function print(Request $request) {
        $title = "Dealer List";

        $print = $request->print;
        $reference = $request->reference;

        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;

        $regionName = RegionSetup::find($region);
        $areaName = AreaSetup::find($area);
        $territoryName = TerritorySetup::find($territory);
        $staffName = StaffSetup::find($reference);

        $dealers = DealerSetup::where('company_id', $this->company)
                ->where('status', 1)
                ->where(function($query) use($region, $area, $territory, $reference) {
                    if ($region) {
                        $query->where('region_id', $region);
                    }
                    if ($area) {
                        $query->where('area_id', $area);
                    }
                    if ($territory) {
                        $query->where('territory_id', $territory);
                    }
                    if ($reference) {
                        $query->where('reference_by', $reference);
                    }
                })
                ->get();

        $pdf = PDF::loadView('admin.dealerList.print', compact('title', 'regionName', 'areaName', 'territoryName', 'staffName', 'dealers'));

        return $pdf->stream('dealer_list.pdf');
    }

    public function dealerListArea(Request $request) {
        $region = $request->region;

        $areas = AreaSetup::where('region_id', $region)->get();

        return $areas;
    }

    public function dealerListTerritory(Request $request) {
        $area = $request->area;

        $territory = TerritorySetup::where('area_id', $area)->get();

        return $territory;
    }

}
