<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use Carbon\Carbon;
use App\RetailSale;
use App\StaffSetup;
use Illuminate\Http\Request;
use App\CustomerRegistration;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use App\Services\Collection\UpComingCollection;
use App\Services\Installment\Collection\CustomerInfo;

class CustomerOutstandingController extends Controller {

    public function index(Request $request) {
        $title = "Customer Outstanding";
        $searchFormLink = "customerOutstanding.index";
        $printFormLink = "customerOutstanding.print";
        $customer = $request->customer;
        $customerIds = $request->customer;
        $dayBefore = $request->dayBefore ? $request->dayBefore : 0;

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->get();
                
        $staffId = $request->staff;

        $customers = CustomerRegistration::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $customerOutstandings = [];

        if ($request->searched) {
            if ($customerIds) {

                $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
            } else if ($staffId) {

                $date = Carbon::now()->subDays($dayBefore)->format('Y-m-d');
                $customerIds = RetailSale::where('sale_date', '<', $date)->where('reference_id', $staffId)->select(['customer_id'])->get()->pluck('customer_id')->toArray();
                $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
            } else {

                $date = Carbon::now()->subDays($dayBefore)->format('Y-m-d');
                $customerIds = RetailSale::where('sale_date', '<', $date)->select(['customer_id'])->get()->pluck('customer_id')->toArray();
                $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
            }
        }

        return view('admin.customerOutstanding.index')->with(compact('staffId', 'staffs', 'dayBefore', 'title', 'searchFormLink', 'printFormLink', 'customers', 'customer', 'customerOutstandings'));
    }

    public function print(Request $request) {
        $title = "Print Customer Outstanding";
        $customer = $request->customer;
        $customerIds = $request->customer;
        $dayBefore = $request->dayBefore ? $request->dayBefore : 0;
        $staffId = $request->staffId;

        $staff = StaffSetup::find($staffId);


        $customerOutstandings = [];

        if ($customerIds) {
            $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
        } else if ($staffId) {

            $date = Carbon::now()->subDays($dayBefore)->format('Y-m-d');
            $customerIds = RetailSale::where('sale_date', '<', $date)->where('reference_id', $staffId)->select(['customer_id'])->get()->pluck('customer_id')->toArray();
            $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
        } else {
            $date = Carbon::now()->subDays($dayBefore)->format('Y-m-d');
            $customerIds = RetailSale::where('sale_date', '<', $date)->select(['customer_id'])->get()->pluck('customer_id')->toArray();
            $customerOutstandings = CustomerInfo::getAllCustomerByCollectorWithCollection($customerIds, $dayBefore);
        }


        $pdf = PDF::loadView('admin.customerOutstanding.print', ['staff' => $staff, 'title' => $title, 'customerOutstandings' => $customerOutstandings]);

        return $pdf->stream('customer_outstandings.pdf');
    }

    public function CustomerSavingsIndex(Request $request) {
        $title = "Customer Savings";
        $searchFormLink = "customerSavings.index";
        $printFormLink = "customerSavings.print";
        $customer = $request->customer;
        $customerIds = $request->customer;
        $amount = $request->amount;


        $retailCustomerIds = RetailSale::distinct('customer_id')->select('customer_id')->get()->pluck('customer_id');

        $customers = CustomerRegistrationSetup::where('status', '1')->whereNotIn('id', $retailCustomerIds)
                ->get();

        $customerOutstandings = [];


        if ($amount == "") {
            $amount = '0-999999999999';
        }


        if ($request->searched) {
            if ($customerIds) {
                $customerOutstandings = CustomerInfo::getAllCustomerWithOutSale($customerIds, $amount);
            } else {

                $customerIds = CustomerRegistrationSetup::select(['id'])->get()->pluck('id')->toArray();

                $customerOutstandings = CustomerInfo::getAllCustomerWithOutSale($customerIds, $amount);
            }
        }

        return view('admin.customerOutstanding.customer_savings')->with(compact('amount', 'title', 'searchFormLink', 'printFormLink', 'customers', 'customer', 'customerOutstandings'));
    }

    public function CustomerSavingsPrint(Request $request) {
        $title = "Print Customer Outstanding";
        $customer = $request->customer;
        $customerIds = $request->customer;
        // $dayBefore = $request->dayBefore ? $request->dayBefore : 0;
        $amount = $request->amount;


        $customerOutstandings = [];

        if ($customerIds) {

            $customerOutstandings = CustomerInfo::getAllCustomerWithOutSale($customerIds, $amount);
        } else {

            $customerIds = CustomerRegistrationSetup::select(['id'])->get()->pluck('id')->toArray();

            $customerOutstandings = CustomerInfo::getAllCustomerWithOutSale($customerIds, $amount);
        }


        $pdf = PDF::loadView('admin.customerOutstanding.customerSavingsPrint', ['title' => $title, 'customerOutstandings' => $customerOutstandings]);

        return $pdf->stream('customer_savings.pdf');
    }

    public function UpCollectionList(Request $request) {
        $title = "UpComing Collection";
        $searchFormLink = "up.collection.index";
        $printFormLink = "up.collection.print";

        $staffs = StaffSetup::where('status', '1')->get();

        $staffId = $request->staff;

        $start_date = Carbon::now()->format('d-m-Y');
        $end_date = Carbon::now()->format('d-m-Y');

        if ($request->has('start_date')) {
            $start_date = date('d-m-Y', strtotime($request->start_date));
        }

        if ($request->has('end_date')) {
            $end_date = date('d-m-Y', strtotime($request->end_date));
        }

        $upComingCollections = [];
        $upComingCollections['installments'] = [];

        if ($request->searched) {

            $upComingCollections = UpComingCollection::Report($start_date, $end_date, $staffId);
        }

        return view('admin.customerOutstanding.upCollectionIndex')->with(compact('staffId', 'staffs', 'title', 'searchFormLink', 'printFormLink', 'start_date', 'end_date', 'upComingCollections'));
    }

    public function UpCollectionListPrint(Request $request) {
        $title = "Print UpComing Collection";

        $start_date = Carbon::now()->format('d-m-Y');
        $end_date = Carbon::now()->format('d-m-Y');
        $staffId = $request->staffId;

        if ($request->start_date) {
            $start_date = date('d-m-Y', strtotime($request->start_date));
        }

        if ($request->end_date) {
            $end_date = date('d-m-Y', strtotime($request->end_date));
        }

        $upComingCollections = UpComingCollection::Report($start_date, $end_date, $staffId);


        $pdf = PDF::loadView('admin.customerOutstanding.upCollectionPrint', ['start_date' => $start_date, 'end_date' => $end_date, 'title' => $title, 'upComingCollections' => $upComingCollections]);

        return $pdf->stream('upcoming_collection.pdf');
    }

}
