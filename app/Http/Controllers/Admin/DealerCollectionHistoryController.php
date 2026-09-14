<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\StaffSetup;
use App\DealerSetup;
use App\DealerCollection;
use App\AdvanceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class DealerCollectionHistoryController extends Controller {

    public function index(Request $request) {
        $title = "Dealer Collection History";
        $searchFormLink = "dealerCollectionHistory.index";
        $printFormLink = "dealerCollectionHistory.print";

        $dealer = $request->dealer;
        $employee = $request->employee;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $employees = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $data = [];

        $collectionHistories = array();

        $collectionHistories = DealerCollection::with(['dealer', 'advance', 'employee'])->orWhere(function ($query) use ($employee, $fromDate, $toDate, $dealer) {
                    if (!empty($fromDate)) {
                        $query->where('payment_date', '>=', $fromDate)
                        ->where('payment_date', '<=', $toDate);
                    }

                    if ($dealer) {
                        $query->whereIn('dealer_id', $dealer);
                    }

                    if ($employee) {
                        $query->whereIn('sale_by', $employee);
                    }
                })
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('payment_date', 'desc')
                ->get();


        foreach ($collectionHistories as $cHistory) {

            if ($cHistory->adjustment == 1) {
                continue;
            }

            $payment = $cHistory->payment_amount;
            if ($cHistory->advance_id !== null) {
                if(!empty($cHistory->advance)){
                    $payment = $payment + $cHistory->advance->advance_amount;
                }
            }

            $ld = [
                'dealerName' => @$cHistory->dealer->name,
                'sale_by' => @$cHistory->employee->name,
                'date' => date('d-m-Y', strtotime($cHistory->payment_date)),
                'paymentNo' => $cHistory->payment_no,
                'bankId' => $cHistory->bank,
                'paymentType' => $cHistory->money_receipt_type,
                'amount' => number_format($payment, 2, '.', ''),
            ];

            array_push($data, $ld);
        }


        $advanceHistory = AdvanceCollection::with(['dealer', 'employee'])
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->where('advance_amount', '>', 0)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('remarks', '!=', "Advance From Collection")
                ->orderBy('date', 'desc');

        if ($dealer) {
            $advanceHistory = $advanceHistory->where('dealer_id', $dealer);
        }

        if ($employee) {
            $advanceHistory = $advanceHistory->whereIn('sale_by', $employee);
        }

        $advanceHistory = $advanceHistory->get();


        foreach ($advanceHistory as $a) {

            if ($a->remarks == 'Advance From Collection') {
                continue;
            }

            $ld = [
                'dealerName' => $a->dealer->name,
                'sale_by' => @$a->employee->name,
                'date' => date('d-m-Y', strtotime($a->date)),
                'paymentNo' => $a->payment_no,
                'bankId' => '',
                'paymentType' => $a->type,
                'amount' => number_format($a->advance_amount, 2, '.', ''),
            ];

            array_push($data, $ld);
        }

        $data = new Collection($data);

        $data = $data->sort(function ($a, $b) {
            return strtotime($a['date']) < strtotime($b['date']);
        });

        $data = $data->toArray();

        return view('admin.dealerCollectionHistory.index')->with(compact('data', 'advanceHistory', 'title', 'searchFormLink', 'printFormLink', 'print', 'dealers', 'employees', 'dealer', 'employee', 'fromDate', 'toDate', 'collectionHistories'));
    }

    public function print(Request $request) {
        $title = "Print Dealer Collection History";

        $dealer = $request->dealer;
        $employee = $request->employee;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $employeName = '';
        if (!empty($employee)) {
            $employeName = StaffSetup::whereIn('id', $employee)->get();
        }

        $collectionHistories = array();
        $data = [];

        $collectionHistories = DealerCollection::with(['dealer', 'advance', 'employee'])->orWhere(function ($query) use ($employee, $fromDate, $toDate, $dealer) {
                    if (!empty($fromDate)) {
                        $query->where('payment_date', '>=', $fromDate)
                        ->where('payment_date', '<=', $toDate);
                    }

                    if ($dealer) {
                        $query->whereIn('dealer_id', $dealer);
                    }

                    if ($employee) {
                        $query->whereIn('sale_by', $employee);
                    }
                })
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('adjustment', 0)
                ->orderBy('payment_date', 'desc')
                ->get();

        $collectionHistories = $collectionHistories->sortBy('dealer.name');

        foreach ($collectionHistories as $cHistory) {

            // if ($cHistory->dealer == null) {
            //     continue;
            // }

            $payment = $cHistory->payment_amount;
            if ($cHistory->advance_id != null) {
                if(!empty($cHistory->advance)){
                    $payment = $payment + $cHistory->advance->advance_amount;
                }
            }

            $ld = [
                'dealerId' => $cHistory->dealer->id,
                'dealerName' => $cHistory->dealer->name,
                'sale_by' => @$cHistory->employee->name,
                'date' => date('d-m-Y', strtotime($cHistory->payment_date)),
                'paymentNo' => $cHistory->payment_no,
                'bankId' => $cHistory->bank,
                'paymentType' => $cHistory->money_receipt_type,
                'amount' => number_format($payment, 2, '.', ''),
            ];

            array_push($data, $ld);
        }

        $advanceHistory = AdvanceCollection::with(['dealer', 'employee'])
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->where('advance_amount', '>', 0)
                ->where('remarks', '!=', "Advance From Collection")
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('date', 'desc');

        if ($dealer) {
            $advanceHistory = $advanceHistory->where('dealer_id', $dealer);
        }

        if ($employee) {
            $advanceHistory = $advanceHistory->whereIn('sale_by', $employee);
        }

        $advanceHistory = $advanceHistory->get();


        foreach ($advanceHistory as $a) {

            if ($a->remarks == 'Advance From Collection') {
                continue;
            }

            $ld = [
                'dealerId' => $a->dealer->id,
                'dealerName' => $a->dealer->name,
                'sale_by' => @$a->employee->name,
                'date' => date('d-m-Y', strtotime($a->date)),
                'paymentNo' => $a->payment_no,
                'bankId' => '',
                'paymentType' => $a->type,
                'amount' => number_format($a->advance_amount, 2, '.', ''),
            ];

            array_push($data, $ld);
        }

        $data = new Collection($data);

        $data = $data->sort(function ($a, $b) {
            return strtotime($a['date']) < strtotime($b['date']);
        });

        $data = $data->toArray();

        $pdf = PDF::loadView('admin.dealerCollectionHistory.print', ['data' => $data, 'advanceHistory' => $advanceHistory, 'title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'collectionHistories' => $collectionHistories, 'employeName' => $employeName]);

        return $pdf->stream('dealer_collection_history_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
