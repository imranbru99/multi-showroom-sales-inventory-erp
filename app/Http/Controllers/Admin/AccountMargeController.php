<?php

namespace App\Http\Controllers\Admin;

use App\CustomerGuarantor;
use Illuminate\Http\Request;
use App\CustomerRegistration;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use App\Installment;
use App\InstallmentCollection;
use App\RetailSale;

class AccountMargeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Account Marge";
        $searchFormLink = "accountMarge.view";
        $printFormLink = "accountMarge.save";

        $customerCode = $request->customer;

        $customers = CustomerRegistration::where('status', '1')
            ->get();


        $data = [];

        if ($request->searched) {
            $data = $customers->where('code', $request->customer);

            // dd($data);
        }

        return view('admin.accountMarge.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'customers', 'data', 'customerCode'));
    }


    public function save(Request $request)
    {
        // dd($request);

        // validate

        $request->validate([
            'selected_customers.*' => 'required'
        ]);

        $allCustomer = $request->selected_customers;


        // get main customer 
        $mainCustomerId = $allCustomer[0];

        $allCustomerWithoutMain = $request->selected_customers;

        unset($allCustomerWithoutMain[0]);

        // all customer Except Main



        // change customer gurantor

        CustomerGuarantor::whereIn('customer_id', $allCustomerWithoutMain)->update([
            'customer_id' => $mainCustomerId
        ]);

        // change customer sale
        RetailSale::whereIn('customer_id', $allCustomerWithoutMain)->update([
            'customer_id' => $mainCustomerId,
        ]);

        // change customer installment

        Installment::whereIn('customer_id', $allCustomerWithoutMain)->update([
            'customer_id' => $mainCustomerId,
        ]);

        // change customer collection
        InstallmentCollection::whereIn('customer_id', $allCustomerWithoutMain)->update([
            'customer_id' => $mainCustomerId,
        ]);


        // delete customer

        CustomerRegistrationSetup::whereIn('id', $allCustomerWithoutMain)->delete();

        return back();
    }
}
