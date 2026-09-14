<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transfer;
use App\TransferProduct;
use App\LiftingProduct;
use App\LiftingReturnProduct;
use DB;
use PDF;
use MPDF;

class TransferReceiveReportController extends Controller {

    public function index(Request $request) {
        $title = "Transfer Receive History";
        $searchFormLink = "transferReceiveReport.index";
        $printFormLink = "transferReceiveReport.print";

        $hostId = $request->host;
        $destinationId = $request->destination;



        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $storeAndShowrooms = DB::table('tbl_stores')
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        $storeIds = DB::table('tbl_stores')
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->get()
                ->pluck('id')
                ->toArray();


        $destinations = DB::table('tbl_stores')
//                ->where('showroom_id', $this->showroomId)
                ->where('company_id', $this->company)
                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        $transferReports = Transfer::with('host', 'destination')
                ->orWhere(function ($query) use ($fromDate, $toDate, $hostId, $storeIds, $destinationId) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($hostId) {
                        $query->where('host_id', $hostId);
                    } else {
                        $query->whereIn('host_id', $storeIds);
                    }

                    if ($destinationId) {
                        $query->where('destination_id', $destinationId);
                    }
                })
                ->whereNotIn('showroom_id', [$this->showroomId])
                ->orderBy('date', 'asc')
                ->get();
                

        return view('admin.transferReceiveReport.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'storeAndShowrooms', 'hostId', 'destinationId', 'fromDate', 'toDate', 'transferReports', 'destinations'));
    }

    public function print(Request $request) {
        $title = "Transfer Receive History";

        ini_set('memory_limit', '9216M');
        ini_set("pcre.backtrack_limit", "50000000");
        ini_set('max_execution_time', '10000');

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $hostType = $request->hostType;
        $hostId = $request->hostId;
        $destinationType = $request->destinationType;
        $destinationId = $request->destinationId;
        $print = $request->print;

        $storeIds = DB::table('tbl_stores')
                ->whereNotIn('showroom_id', [$this->showroomId])
                ->get()
                ->pluck('id')
                ->toArray();


        $destinations = DB::table('tbl_stores')
                ->where('showroom_id', $this->showroomId)
                ->pluck('id')
                ->toArray();

        $transferReports = Transfer::with('host', 'destination')
                ->orWhere(function ($query) use ($fromDate, $toDate, $hostId, $storeIds, $destinationId, $destinations) {
                    if (!empty($fromDate)) {
                        $query->whereBetween('date', array($fromDate, $toDate));
                    }

                    if ($hostId) {
                        $query->where('host_id', $hostId);
                    } else {
                        $query->whereIn('host_id', $storeIds);
                    }

                    if ($destinationId) {
                        $query->where('destination_id', $destinationId);
                    } else {
                        $query->whereIn('destination_id', $destinations);
                    }
                })
                ->whereNotIn('showroom_id', [$this->showroomId])
                ->orderBy('date', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.transferReceiveReport.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'transferReports' => $transferReports]);

        return $pdf->stream('transfer_receive_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
