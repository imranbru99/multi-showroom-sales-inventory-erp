<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceProductReceive;
use App\ServiceDelivery;
use App\ServiceAllocation;
use DB;
use PDF;

class ServiceReportController extends Controller {

    public function index(Request $request) {
        $title = "Servicing History";
        $searchFormLink = "serviceReport.index";
        $printFormLink = "serviceReport.print";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $type = $request->type;



        $serviceProductReceives = [];
        $serviceRunningAllocation = [];
        $inStock = [];
        $serviceDeliveries = [];
        $serviceDamages = [];

        if ($type == 'receive') {
            $title = "Product Receive History";

            $serviceProductReceives = ServiceProductReceive::with('dealer', 'product', 'remarks')
                    ->where('receive_date', '>=', $fromDate)
                    ->where('receive_date', '<=', $toDate)
                    ->where('showroom_id', $this->showroomId)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        if ($type == 'running_allocation') {
            $title = "Running Allocation History";

            $serviceRunningAllocation = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'remarks')
                    ->where('showroom_id', $this->showroomId)
                    ->where('issue_date', '>=', $fromDate)
                    ->where('issue_date', '<=', $toDate)
                    ->where('status', 'Initial')
                    ->get();
        }

        if ($type == 'stock') {
            $title = "Service In Stock History";

            $delivery = ServiceDelivery::select('service_allocation_id')
                    ->get()
                    ->pluck('service_allocation_id')
                    ->toArray();

            $inStock = ServiceAllocation::where('status', 'Complete')
                    ->where('finish_date', '>=', $fromDate)
                    ->where('finish_date', '<=', $toDate)
                    ->whereNotIn('id', $delivery)
                    ->get();
        }

        if ($type == 'delivery') {
            $title = "Delivery History";

            $serviceDeliveries = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'staff')
                    ->where('showroom_id', $this->showroomId)
                    ->where('delivery_date', '>=', $fromDate)
                    ->where('delivery_date', '<=', $toDate)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        if ($type == 'damage') {
            $title = "Damage History";

            $serviceDamages = ServiceProductReceive::with('dealer', 'product', 'remarks')
                    ->where('receive_date', '>=', $fromDate)
                    ->where('receive_date', '<=', $toDate)
                    ->where('product_condition', 'Damage')
                    ->where('showroom_id', $this->showroomId)
                    ->orderBy('id', 'desc')
                    ->get();
        }




        return view('admin.servicingReport.index')->with(compact(
                                'title',
                                'searchFormLink',
                                'printFormLink',
                                'print',
                                'fromDate',
                                'toDate',
                                'type',
                                'serviceProductReceives',
                                'serviceRunningAllocation',
                                'inStock',
                                'serviceDeliveries',
                                'serviceDamages'
        ));
    }

    public function print(Request $request) {
        $title = "Service History";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $type = $request->type;



        $serviceProductReceives = [];
        $serviceRunningAllocation = [];
        $inStock = [];
        $serviceDeliveries = [];
        $serviceDamages = [];


        if ($type == 'receive') {
            $title = "Product Receive History";

            $serviceProductReceives = ServiceProductReceive::with('dealer', 'product', 'remarks')
                    ->where('receive_date', '>=', $fromDate)
                    ->where('receive_date', '<=', $toDate)
                    ->where('showroom_id', $this->showroomId)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        if ($type == 'running_allocation') {
            $title = "Running Allocation History";

            $serviceRunningAllocation = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'remarks')
                    ->where('showroom_id', $this->showroomId)
                    ->where('issue_date', '>=', $fromDate)
                    ->where('issue_date', '<=', $toDate)
                    ->where('status', 'Initial')
                    ->get();
        }

        if ($type == 'stock') {
            $title = "In Stock History";

            $delivery = ServiceDelivery::select('service_allocation_id')
                    ->get()
                    ->pluck('service_allocation_id')
                    ->toArray();

            $inStock = ServiceAllocation::where('status', 'Complete')
                    ->where('finish_date', '>=', $fromDate)
                    ->where('finish_date', '<=', $toDate)
                    ->whereNotIn('id', $delivery)
                    ->get();
        }

        if ($type == 'delivery') {
            $title = "Delivery History";

            $serviceDeliveries = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'staff')
                    ->where('showroom_id', $this->showroomId)
                    ->where('delivery_date', '>=', $fromDate)
                    ->where('delivery_date', '<=', $toDate)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        if ($type == 'damage') {
            $title = "Damage History";

            $serviceDamages = ServiceProductReceive::with('dealer', 'product', 'remarks')
                    ->where('receive_date', '>=', $fromDate)
                    ->where('receive_date', '<=', $toDate)
                    ->where('product_condition', 'Damage')
                    ->where('showroom_id', $this->showroomId)
                    ->orderBy('id', 'desc')
                    ->get();
        }


        $pdf = PDF::loadView('admin.servicingReport.print', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'type' => $type,
                    'serviceProductReceives' => $serviceProductReceives,
                    'serviceRunningAllocation' => $serviceRunningAllocation,
                    'inStock' => $inStock,
                    'serviceDeliveries' => $serviceDeliveries,
                    'serviceDamages' => $serviceDamages,
        ]);

        return $pdf->stream($type . '_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
