<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StoreSetup;
use App\Transfer;
use DB;
use PDF;
use MPDF;

class TransferReportController extends Controller {

    public function index(Request $request) {
        $title = "Transfer History Report";
        $searchFormLink = "transferReport.index";
        $printFormLink = "transferReport.print";

        $hostId = $request->host;
        $destinationId = $request->destination;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $stores = StoreSetup::where('company_id', $this->company)->get();

        $storeIds = DB::table('tbl_stores')
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->get()
                ->pluck('id')
                ->toArray();

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
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->orderBy('date', 'asc')
                ->get();


        return view('admin.transferReport.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'stores', 'hostId', 'destinationId', 'fromDate', 'toDate', 'transferReports'));
    }

    public function print(Request $request) {
        $title = "Transfer History Report";

        $hostId = $request->host;
        $destinationId = $request->destination;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $stores = StoreSetup::where('company_id', $this->company)->get();

        $storeIds = DB::table('tbl_stores')
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->get()
                ->pluck('id')
                ->toArray();

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
//                ->whereNotIn('showroom_id', [$this->showroomId])
                ->where('company_id', $this->company)
                ->orderBy('date', 'asc')
                ->get();

        $pdf = PDF::loadView('admin.transferReport.print', [
                    'title' => $title,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'transferReports' => $transferReports
                        ], [], ['orientation' => 'L']);

        return $pdf->stream('transfer_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
