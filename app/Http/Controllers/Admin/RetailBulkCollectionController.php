<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ArrayHelper;
use Illuminate\Http\Request;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use App\Services\Collection\CustomerWiseCollection;

class RetailBulkCollectionController extends Controller
{
    public function add(Request $request)
    {
        $title = "Retail Bulk Collection";
        $customers = CustomerRegistrationSetup::all();
        $customerId = $request->customer;

        $data = [];

        if ($request->searched) {
            $data = CustomerWiseCollection::get($customerId);
            // dd($data);
        }

        return view('admin.retailBulkCollection.index', compact('title', 'customers', 'data', 'customerId'));
    }

    public function save(Request $request)
    {

        $customerAmountIds = $request->id;

        if ($customerAmountIds) {
            $allAmounts = $request->amount;

            $zeroCustomerAmounts = ArrayHelper::RemoveArrayitems($request->amount, $customerAmountIds);
            $zeroCustomerIds = array_keys($zeroCustomerAmounts);


            // save customer Ids
            $aa = InstallmentCollection::whereIn('id', $customerAmountIds)->update([
                'customer_id' => $request->customerId,
            ]);

            // save zero Ids
            InstallmentCollection::whereIn('id', $zeroCustomerIds)->update([
                'customer_id' => 0,
            ]);
        } else {
            // save zero Ids
            InstallmentCollection::where('customer_id', $request->customerId)->update([
                'customer_id' => 0,
            ]);
        }


        return back();
    }


    public function getExportData(Request $request)
    {
        return CustomerWiseCollection::getWithDate(0, $request->startDate, $request->endDate, $request->memo);
    }
}
