<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceDelivery;
use DB;
use PDF;

class ServiceProductDeliveryListController extends Controller {

    public function index(Request $request) {
        $title = "Service Product Delivery";

        $searchFormLink = 'serviceProductDeliveryList.index';
        $printFormLink = 'serviceProductDeliveryList.print';

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $serviceDeliveries = [];
        if ($request->print) {
            $serviceDeliveries = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'staff')
                    ->where('showroom_id', $this->showroomId)
                    ->whereBetween('delivery_date', [$fromDate, $toDate])
                    ->orderBy('id', 'desc')
                    ->get();
        }

        return view('admin.serviceProductDeliveryList.index')->with(compact('title', 'serviceDeliveries', 'fromDate', 'toDate', 'print', 'searchFormLink', 'printFormLink'));
    }

    public function print(Request $request) {
        $title = "Service Product Delivery";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $serviceDeliveries = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'staff')
                    ->where('showroom_id', $this->showroomId)
                    ->whereBetween('delivery_date', [$fromDate, $toDate])
                    ->orderBy('id', 'desc')
                    ->get();

        $pdf = PDF::loadView('admin.serviceProductDeliveryList.print', ['serviceDeliveries' => $serviceDeliveries, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate], [], ['format' => 'A4-L']);

        return $pdf->stream('service_product_delivery_list_' . $fromDate . 'to' . $toDate . '.pdf');
    }

}
