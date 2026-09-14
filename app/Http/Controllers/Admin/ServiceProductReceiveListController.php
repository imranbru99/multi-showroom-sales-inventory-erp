<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceProductReceive;
use DB;
use PDF;

class ServiceProductReceiveListController extends Controller {

    public function index(Request $request) {
        $title = "Service Product Receive";

        $searchFormLink = 'serviceProductReceiveList.index';
        $printFormLink = 'serviceProductReceiveList.print';

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $serviceProductReceives = [];
        if ($request->print) {
            $serviceProductReceives = ServiceProductReceive::with('dealer', 'product', 'remarks')
                    ->where('showroom_id', $this->showroomId)
                    ->whereBetween('receive_date', [$fromDate, $toDate])
                    ->orderBy('id', 'desc')
                    ->get();
        }

        return view('admin.serviceProductReceiveList.index')->with(compact('title', 'serviceProductReceives', 'fromDate', 'toDate', 'print', 'searchFormLink', 'printFormLink'));
    }

    public function print(Request $request) {
        $title = "Service Product Receive";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $serviceProductReceives = ServiceProductReceive::with('dealer', 'product', 'remarks')
                ->where('showroom_id', $this->showroomId)
                ->whereBetween('receive_date', [$fromDate, $toDate])
                ->orderBy('id', 'desc')
                ->get();

        $pdf = PDF::loadView('admin.serviceProductReceiveList.print', ['serviceProductReceives' => $serviceProductReceives, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate]);

        return $pdf->stream('service_product_receive_list_' . $fromDate . 'to' . $toDate . '.pdf');
    }

}
