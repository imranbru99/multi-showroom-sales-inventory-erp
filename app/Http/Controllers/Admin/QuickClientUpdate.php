<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\RetailSale;
use App\StaffSetup;
use App\CustomerAgreement;
use Illuminate\Http\Request;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;

class QuickClientUpdate extends Controller
{
    public function index(Request $request)
    {
        $title = "Quick Client Update";
        $references = StaffSetup::where('status', 1)->get();
        $data = [];
        $data['customers'] = [];
        $referenceId = "";

        $referenceId = $request->reference;

        $no_refer = $request->reference;

        $account_no = $request->account_no;



        if ($request->has('reference')) {

            // fetch Sale Customers
            $RetailCustomerIds = RetailSale::select(['customer_id'])
                ->groupBy('customer_id');

            if ($referenceId) {
                $RetailCustomerIds = $RetailCustomerIds->where('reference_id', $referenceId);
            }

            $RetailCustomerIds = $RetailCustomerIds->get()
                ->pluck('customer_id')
                ->toArray();


            // Agreement customer Ids
            $agreementCustomerIds = CustomerAgreement::select(['customer_id'])
                ->groupBy('customer_id');

            if ($referenceId) {
                if ($no_refer == "no_refence") {
                    $agreementCustomerIds = $agreementCustomerIds->whereNull('employee_id');
                } else {
                    $agreementCustomerIds = $agreementCustomerIds->where('employee_id', $referenceId);
                }
            }

            $agreementCustomerIds = $agreementCustomerIds->get()
                ->pluck('customer_id')
                ->toArray();



            // join customer ids in one array 
            $customerIds = array_merge($RetailCustomerIds, $agreementCustomerIds);

            // fetch Customer Data

            $data['customers'] = CustomerRegistrationSetup::whereIn('id', $customerIds)->where('code', '!=', 'cash')->get();
        }

        if (!empty($account_no)) {

            // $RetailCustomerIds = RetailSale::select(['customer_id'])
            //     ->groupBy('customer_id')->get()
            //     ->pluck('customer_id')
            //     ->toArray();

            $RetailCustomerIds = [];

            // Agreement customer Ids
            $agreementCustomerIds = CustomerAgreement::select(['customer_id'])
                ->groupBy('customer_id')
                ->where('account_no', 'like', '%' . $account_no . '%')
                ->get()
                ->pluck('customer_id')
                ->toArray();



            // join customer ids in one array 
            $customerIds = array_merge($RetailCustomerIds, $agreementCustomerIds);

            // fetch Customer Data

            $data['customers'] = CustomerRegistrationSetup::whereIn('id', $customerIds)->where('code', '!=', 'cash')->get();
        }

        return view('admin.quickClientUpdate.index', compact('title', 'references', 'data', 'referenceId', 'no_refer', 'account_no'));
    }


    public function updateClientReferences(Request $request)
    {

        $customer = $request->customerId;
        $reference = $request->reference;
        $freezeStatus = (int)filter_var($request->freezeStatus, FILTER_VALIDATE_BOOLEAN);

        $customer = CustomerRegistrationSetup::where('id', $customer)->first();

        // update customer freeze
        $customer->update([
            'freeze' => $freezeStatus,
        ]);



        // update reference
        RetailSale::where('customer_id', $customer->id)->update([
            'reference_id' => $reference,
        ]);

        // update agreement
        CustomerAgreement::where('customer_id', $customer->id)->update([
            'employee_id' => $reference,
        ]);


        // dd($request->freezeStatus == true);

    }


    public function CustomerAgreementReport(Request $request)
    {
        $title = "Customer Agreements List";
        $startDate = date('Y-m-d', strtotime('2019-09-01'));
        $endDate = date('Y-m-d', strtotime(now()));
        $collector_id = $request->collector;
        $agreements = [];

        $collectors = StaffSetup::where('status', 1)->get();

        if ($request->has('searched')) {

            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate = date('Y-m-d', strtotime($request->end_date));

            $agreements = CustomerAgreement::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->with(['staff', 'customer']);

            if ($collector_id) {
                $agreements = $agreements->where('employee_id', $collector_id);
            }

            $agreements = $agreements->get();

            // dd($request);

            if ($request->Fsubmit == 'searched') {
                return view('admin.quickClientUpdate.customer_agreement_index', compact('title', 'startDate', 'endDate', 'agreements', 'collectors', 'collector_id'));
            } else {
                $pdf = PDF::loadView('admin.quickClientUpdate.customerAgreementPrint', ['title' => $title, 'agreements' => $agreements]);

                return $pdf->stream('customer_savings.pdf');
            }
        }




        return view('admin.quickClientUpdate.customer_agreement_index', compact('title', 'startDate', 'endDate', 'agreements', 'collectors', 'collector_id'));
    }
}
