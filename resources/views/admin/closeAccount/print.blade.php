@extends('admin.layouts.masterPrint')
@php
use Carbon\Carbon;
$collectionIds = \App\InstallmentCollection::where('customer_id', $customer->customer_id)
    ->select('id')
    ->get()
    ->pluck('id');
$collectionList = \App\InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
    ->orderBy('installment_collection_date', 'asc')
    ->get();

$start_date = Carbon::parse(@$collectionList[0]->installment_collection_date);
$last_col_date = Carbon::parse(@$collectionList[count($collectionList) - 1]->installment_collection_date);

$sale = \App\RetailSale::where('id', $customer->sale_id)->first();
$due = $customer->invoice_amount - $customer->collection_amount;
// if ($due > 0) {
//     $balance = $due + $customer->late_fee - ($customer->discount_amount + $customer->missing_amount + $customer->interest_receive);
// } else {
//     $balance = $customer->late_fee - ($customer->discount_amount + $customer->missing_amount + $customer->interest_receive);
// }

$balance = $due + $customer->late_fee - ($customer->discount_amount + $customer->missing_amount + $customer->interest_receive);

@endphp
@section('content')
    <table id="report-header">
        <tr>
            <td>
                Close Account
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <div class="row AccountDetails">
        <div class="col-md-6 pr-4">
            <div class="card-body p-0">
                <h4 class="mb-3" style="border-bottom: 1px solid black;">Account Details</h4>
                <table width="100%" class="account_info">
                    <thead>
                        <th width="40%"></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="40%"> <b>Account No</b></td>
                            <td>: {{ @$customer->customer->code }}</td>
                        </tr>
                        <tr>
                            <td width="40%"> <b>Account Name</b></td>
                            <td>: {{ @$customer->customer->name }}
                        </tr>
                        <tr>
                            <td width="40%"> <b>Phone No</b></td>
                            <td>: {{ @$customer->customer->phone_no }}</td>
                        </tr>
                        <tr>
                            <td width="40%"> <b>Adress</b></td>
                            <td>: {{ @$customer->customer->present_address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-body p-0 mt-5">
                <h4 class="mb-3" style="border-bottom: 1px solid black;">Product Name:
                    <span class="font-weight-bold product">
                        {{ @$customer->sale->products[0]->product->name }}
                    </span>
                </h4>
                <table width="100%" class="product_info">
                    <thead>
                        <th width="40%"></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Model No </b> </td>
                            <td class="model_no">: {{ @$customer->sale->products[0]->product->model_no }}</td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Sale Type </b> </td>
                            <td class="sale_type">: {{ @$customer->sale->sale_type }} </td>
                        </tr>
                        <tr>
                            <td width="40%" class="pt-4"><b class="font-weight-bold">Install. Type </b> </td>
                            <td class="pt-4 install_type">: {{ @$customer->sale->installment_type }} </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Install. Qty </b> </td>
                            <td class="inst_qty">: {{ count($collectionList) }} </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Invoice Date </b> </td>
                            <td class="first_ins">: {{ date('d-m-Y', strtotime(@$sale->sale_date)) }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Install. Start Date </b> </td>
                            <td class="first_ins">: {{ date('d-m-Y', strtotime(@$customer->sale->sale_date)) }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Install. End Date </b> </td>
                            <td class="last_ins">:
                                {{ date('d-m-Y', strtotime(@$collectionList[count($collectionList) - 1]->installment_collection_date)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-md-6 pl-4">
            <h4 class="mb-3" style="border-bottom: 1px solid black;">Settlement Information -
                (#Invoice-{{ @$customer->invoice_no }})</h4>
            <div class="card-body p-0">
                <table width="100%" class="invoice_info">
                    <thead>
                        <th width="40%"></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Invoice Amount </b></td>
                            <td class="text-left">: <span
                                    class="sale_price">{{ $customer->invoice_amount }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Collection </b></td>
                            <td class="total_coll">: {{ $customer->collection_amount }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Due </b></td>
                            <td class="due">:
                                {{ $due > 0 ? $due : 0 }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Over Collection </b></td>
                            <td class="due">:
                                {{ $due < 0 ? -1 * $due : 0 }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Late Days </b></td>
                            <td>: {{ $customer->late_days }}</td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Late Fee: </b></td>
                            <td>: {{ $customer->late_fee }} </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Late Fee Receive </b></td>
                            <td>: {{ $customer->interest_receive }} </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Discount Amount </b></td>
                            <td> : {{ $customer->discount_amount }} </td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Cumulative Due </b></td>
                            <td>: {{ $balance }}</td>
                        </tr>
                        <tr>
                            <td width="40%"><b class="font-weight-bold">Agreement Amount </b></td>
                            <td>: {{ @$customer->agreement->agreement_amount }}</td>
                        </tr>

                        @if ($customer->employee_id)
                            <tr>
                                <td width="40%"><b class="font-weight-bold">Employee</b></td>
                                <td>: {{ @$customer->staff->name }}</td>
                            </tr>
                            <tr>
                                <td width="40%"><b class="font-weight-bold">Missing Amount </b></td>
                                <td>: {{ $customer->missing_amount }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td width="40%"><b class="font-weight-bold">Remarks </b></td>
                            <td>: {{ @$customer->remarks }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>

@endsection
