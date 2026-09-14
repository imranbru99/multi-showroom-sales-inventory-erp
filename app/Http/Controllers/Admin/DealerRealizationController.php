<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\DealerSetup;
use App\ProductIssue;
use App\AdvanceCollection;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Realization\Dealer\DealerRealization;

class DealerRealizationController extends Controller {

    public function index(Request $request) {
        $title = "Dealer Realization";
        $searchFormLink = "dealerRealization.index";
        $printFormLink = "dealerRealization.print";

        $dealer = $request->dealer;
        $dealer_type = $request->dealer_type;
        $month = $request->month;
        $year = $request->year;
        $print = $request->print;
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;

        $regions = RegionSetup::where('company_id', $this->company)->get();

        $areas = [];
        $territories = [];
        if ($region) {
            $areas = AreaSetup::where('company_id', $this->company)
                    ->where('region_id', $region)
                    ->get();
        }
        if ($area) {
            $territories = TerritorySetup::where('company_id', $this->company)
                    ->where('area_id', $area)
                    ->get();
        }

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory) {
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $dealerIds = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory, $dealer) {
                    if ($dealer) {
                        $q->whereIn('id', $dealer);
                    }
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('id')
                ->toArray();

        $data = DealerRealization::getDealerRealization($dealer_type, $dealerIds, $month, $year, $this->company);


        return view('admin.dealerRealization.index')->with(compact(
                                'title',
                                'dealer_type',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'dealers',
                                'dealer',
                                'month',
                                'year',
                                'data',
                                'region',
                                'regions',
                                'area',
                                'areas',
                                'territory',
                                'territories',
        ));
    }

    public function print(Request $request) {
        $title = "Dealer Realization";

        $dealer = $request->dealer;
        $dealer_type = $request->dealer_type;
        $month = $request->month;
        $year = $request->year;
        $region = $request->region;
        $area = $request->area;
        $territory = $request->territory;


        $dealerIds = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where(function($q) use ($region, $area, $territory, $dealer) {
                    if ($dealer) {
                        $q->whereIn('id', $dealer);
                    }
                    if ($region) {
                        $q->where('region_id', $region);
                    }
                    if ($area) {
                        $q->where('area_id', $area);
                    }
                    if ($territory) {
                        $q->where('territory_id', $territory);
                    }
                })
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('id')
                ->toArray();

        $data = DealerRealization::getDealerRealization($dealer_type, $dealerIds, $month, $year, $this->company);

        $pdf = PDF::loadView('admin.dealerRealization.print', ['title' => $title, 'year' => $year, 'month' => $month, 'data' => $data]);

        return $pdf->stream('dealer_realization_' . $month . '_of_' . $year . '.pdf');
    }

}
