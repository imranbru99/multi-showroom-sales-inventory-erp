<?php

namespace App\Http\Controllers\Admin;

use App\RetailSale;
use App\Helper\ArrayHelper;
use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use App\Installment;
use App\Services\Collection\CustomerWiseSale;

class RetailBulkSaleControler extends Controller
{
    public function add(Request $request)
    {
        $title = "Retail Bulk Sale";
        $customers = CustomerRegistrationSetup::all();
        $customerId = $request->customer;

        $data = [];

        if ($request->searched) {
            $data = CustomerWiseSale::get($request->customer);
        }

        return view('admin.retailBulkSale.index', compact('title', 'customers', 'data', 'customerId'));
    }

    public function save(Request $request)
    {

        $customerAmountIds = $request->id;

        if ($customerAmountIds) {

            $allAmounts = $request->amount;

            $zeroCustomerAmounts = ArrayHelper::RemoveArrayitems($request->amount, $customerAmountIds);
            $zeroCustomerIds = array_keys($zeroCustomerAmounts);

            // save customer Ids
            $retailInvoices = RetailSale::whereIn('id', $customerAmountIds)->select(['invoice_no'])->get()->pluck('invoice_no')->toArray();

            Installment::whereIn('invoice_no', $retailInvoices)->update([
                'customer_id' => $request->customerId,
            ]);

            RetailSale::whereIn('id', $customerAmountIds)->update([
                'customer_id' => $request->customerId,
            ]);

            // save zero Ids
            $zeroInvoices = RetailSale::whereIn('id', $zeroCustomerIds)->select(['invoice_no'])->get()->pluck('invoice_no')->toArray();

            Installment::whereIn('invoice_no', $zeroInvoices)->update([
                'customer_id' => $request->customerId,
            ]);

            RetailSale::whereIn('id', $zeroCustomerIds)->update([
                'customer_id' => 0,
            ]);
        } else {
            RetailSale::where('customer_id', $request->customerId)->update([
                'customer_id' => 0,
            ]);
        }

        return redirect()->route('bulk.sale.add');
    }


    public function getExportData(Request $request)
    {
        return CustomerWiseSale::getWithDate(0, $request->date);
    }
}
