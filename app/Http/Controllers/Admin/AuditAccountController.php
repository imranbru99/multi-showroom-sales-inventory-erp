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

class AuditAccountController extends Controller
{
    public function index(Request $request)
    {
        $title = "Audit Accounts";
        $references = StaffSetup::where('status', 1)->get();
        $data = [];
        $data['customers'] = [];

        $referenceId = "";

        $referenceId = $request->reference;


        if (!empty($request->reference)) {

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
                $agreementCustomerIds = $agreementCustomerIds->where('employee_id', $referenceId);
            }

            $agreementCustomerIds = $agreementCustomerIds->get()
                ->pluck('customer_id')
                ->toArray();

            // join customer ids in one array 
            $customerIds = array_merge($RetailCustomerIds, $agreementCustomerIds);

            // fetch Customer Data

            $data['customers'] = CustomerRegistrationSetup::whereIn('id', $customerIds)
                ->where('code', '!=', 'cash')
                ->where('is_audit', 0)
                ->get();
        } else {
            $data['customers'] = CustomerRegistrationSetup::where('code', '!=', 'cash')
                ->where('is_audit', 0)
                ->get();
        }

        return view('admin.auditAccount.index', compact('title', 'references', 'data', 'referenceId'));
    }


    public function updateClientReferences(Request $request)
    {

        $customer = $request->customerId;
        $auditStatus = (int)filter_var($request->isAudit, FILTER_VALIDATE_BOOLEAN);

        $customer = CustomerRegistrationSetup::where('id', $customer)->first();

        $customer->update([
            'is_audit' => $auditStatus,
        ]);

        return $customer;
    }

    public function print(Request $request)
    {
        $title = "Not Audit";

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        // $customers = CustomerRegistrationSetup::select(['code', 'name', 'phone_no', 'present_address'])
        //     ->where('code', '!=', 'cash')
        //     ->where('is_audit', 0)
        //     ->orderBy('code', 'asc')
        //     ->get();
        $references = StaffSetup::where('status', 1)->get();
        $customers = array();
        $referenceId = "";

        $referenceId = $request->reference;

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
                $agreementCustomerIds = $agreementCustomerIds->where('employee_id', $referenceId);
            }

            $agreementCustomerIds = $agreementCustomerIds->get()
                ->pluck('customer_id')
                ->toArray();

            // join customer ids in one array 
            $customerIds = array_merge($RetailCustomerIds, $agreementCustomerIds);

            // fetch Customer Data

            $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)
                ->where('code', '!=', 'cash')
                ->where('is_audit', 0)
                ->get();
        }


        $pdf = PDF::loadView('admin.auditAccount.print', ['title' => $title, 'customers' => $customers]);

        return $pdf->stream('customer_not_audit.pdf');
    }
}
