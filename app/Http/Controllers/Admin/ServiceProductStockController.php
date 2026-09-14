<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceDelivery;
use App\ServiceAllocation;
use DB;
use PDF;

class ServiceProductStockController extends Controller {

    public function index() {
        $title = "Service Product Stock";

        $serviceDelivery = ServiceDelivery::select('service_allocation_id')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('service_allocation_id')
                ->toArray();

        $completeAllocations = ServiceAllocation::where('status', 'Complete')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->whereNotIn('id', $serviceDelivery)
                ->get();

        return view('admin.serviceStock.index')->with(compact('title', 'completeAllocations'));
    }

    public function print(Request $request) {
        $title = "Service Product Stock";

        $serviceDelivery = ServiceDelivery::select('service_allocation_id')
                ->get()
                ->pluck('service_allocation_id')
                ->toArray();

        $completeAllocations = ServiceAllocation::where('status', 'Complete')
                ->whereNotIn('id', $serviceDelivery)
                ->get();

        $pdf = PDF::loadView('admin.serviceStock.print', ['completeAllocations' => $completeAllocations, 'title' => $title], [], ['format' => 'A4-L']);

        return $pdf->stream('service_stock.pdf');
    }

}
