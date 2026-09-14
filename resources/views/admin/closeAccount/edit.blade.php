@extends('admin.layouts.masterAddEdit')
@php
use Carbon\Carbon;
$collectionIds = \App\InstallmentCollection::where('customer_id', $customer->customer_id)
    ->select('id')
    ->get()
    ->pluck('id');
$collectionList = \App\InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
    ->orderBy('installment_collection_date', 'asc')
    ->get();

$returns = \App\RetailSalesReturnProducts::with('return')
        ->whereHas('return', function ($q) use ($customer) {
            $q->where('customer_id', $customer->customer_id);
        })
        ->sum('sales_price');

//Sales
$total_sales = @$customer->sale->products->sum('sales_price');
$total_discount = @$customer->sale->products->sum('discount');
$total_voucher = @$customer->sale->products->sum('gift_voucher');
$total_excrt = @$customer->sale->products->sum('exchange_crt');
$sale = $total_sales - ($total_discount + $total_voucher + $total_excrt);
$sale = $sale - $returns;

$collections = 0;
foreach ($collectionList as $collection) {
    $collections += floatval(@$collection->installment_schedule_amount);
}

$outstanding = $sale - $collections;

$start_date = @$customer->sale->sale_date;
$last_col_date = Carbon::parse(@$collectionList[count($collectionList) - 1]->installment_collection_date);

$late_days = $last_col_date->diffInDays($start_date);

$typeDays = 0;
if ($customer->sale->sale_type == 'Long Installment') {
    $typeDays = 365;
} elseif ($customer->sale->sale_type == 'Short Installment') {
    $typeDays = 183;
} else {
    $typeDays = 0;
}
$duration = $late_days - $typeDays;

if ($duration < 0) {
    $duration = 0;
}

$late_fee = 0;
if ($duration > 0) {
    $total = $sale * 0.12;
    $fee = $total / 365;
    if ($customer->sale->sale_type == 'Long Installment') {
        $late_fee = round($fee * $duration);
    } else {
        $late_fee = round($fee * ($duration + 183));
    }
}
// if ($customer->is_settle == 0) {
//     $total_due = $sale + $late_fee + $customer->interest_receive - ($collections + $customer->discount_amount + $customer->missing_amount);
//     $main_due = $late_fee + $outstanding;
// } else {
//     $total_due = $sale + $customer->interest_receive - ($collections + $customer->discount_amount + $customer->missing_amount);
//     $main_due = $outstanding;
// }

    $total_due = $outstanding + $customer->late_fee - ($customer->discount_amount + $customer->missing_amount + $customer->interest_receive);


@endphp
@section('card_body')
    <div class="card-body">
        <div class="row d-flex justify-content-between">

            <div class="form-group col-md-3">
                <label for="account_no">Date</label>
                <input type="text" class="form-control datepicker" id="account_no" name="date"
                    value="{{ date('d-m-Y', strtotime($customer->close_date)) }}">
            </div>
        </div>
        <div class="row AccountDetails">
            <div class="col-md-6 pr-4" style="border-right: 1px solid black;">
                <div class="card-body p-0">
                    <h4 class="mb-3" style="border-bottom: 1px solid black;">Account Details</h4>
                    <table width="100%">
                        <thead>
                            <th width="35%"></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td> <label for="accunt" class="mr-2">Account No</label></td>
                                <td>
                                    <input type="text" class="form-control account_code" name="account_name"
                                        value="{{ @$customer->customer->code }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td> <label for="name" class="mr-2">Account Name</label></td>
                                <td>
                                    <input type="text" class="form-control account_name" name="account_name"
                                        value="{{ @$customer->customer->name }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td> <label for="phone_no" class="mr-2">Phone No</label></td>
                                <td>
                                    <input type="text" class="form-control phone_no" name="phone_no"
                                        value="{{ @$customer->customer->phone_no }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td> <label for="address" class="mr-2">Adress</label></td>
                                <td>
                                    <textarea class="form-control address" name="address"
                                        readonly>{{ @$customer->customer->present_address }}</textarea>
                                </td>
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
                    <table>
                        <thead>
                            <th width="35%"></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b class="font-weight-bold">Model No </b> </td>
                                <td class="model_no">: {{ @$customer->sale->products[0]->product->model_no }}</td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Sale Type </b> </td>
                                <td class="sale_type">: {{ @$customer->sale->sale_type }} </td>
                            </tr>
                            <tr>
                                <td class="pt-4"><b class="font-weight-bold">Install. Type </b> </td>
                                <td class="pt-4 install_type">: {{ @$customer->sale->installment_type }} </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Install. Qty </b> </td>
                                <td class="inst_qty">: {{ count($collectionList) }} </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Invoice Date </b> </td>
                                <td class="first_ins">: {{ date('d-m-Y', strtotime(@$customer->sale->sale_date)) }}
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Install. Start Date </b> </td>
                                <td class="first_ins">:
                                    {{ date('d-m-Y', strtotime(@$collectionList[0]->installment_collection_date)) }}
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Install. End Date </b> </td>
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
                    <table width="100%">
                        <thead>
                            <th width="40%"></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b class="font-weight-bold">Invoice Amount </b></td>
                                <td class="text-left">: <span class="sale_price">{{ $sale }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Collection: </b></td>
                                <td class="total_coll">: {{ $collections }}
                                </td>
                            </tr>

                            <tr>
                                <td><b class="font-weight-bold">Due: </b></td>
                                <td class="due">:
                                    {{ $outstanding > 0 ? $outstanding : 0 }}
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Over Collection: </b></td>
                                <td class="due">:
                                    {{ $outstanding < 0 ? -1 * $outstanding : 0 }}
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Late Days: </b></td>
                                <td>: <span class="late_days">{{ $duration }}</span> <input type="checkbox"
                                        class="ml-5 on_schedule" onclick="schedule({{ $customer->customer_id }})"
                                        @if ($customer->is_schedule == 1) checked @endif> <span class="ml-2">On Schedule</span></td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Late Fee: </b></td>
                                <td>: <span class="late_fee">{{ $late_fee }}</span> </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Interest Receive: </b></td>
                                <td> <input type="number" class="form-control interest_receive" name="interest_receive"
                                        value="{{ $customer->interest_receive }}" onkeyup="calculate()"
                                        placeholder="Amount"></td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Discount Amount: </b></td>
                                <td> <input type="number" class="form-control discount_amount" name="discount_amount"
                                        value="{{ $customer->discount_amount }}" onkeyup="calculate()"
                                        placeholder="Amount"></td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Cumulative Due </b></td>
                                <td><input type="number" class="form-control cam_due" name="cam_due"
                                        value="{{ $total_due }}" placeholder="" readonly></td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Employee</b></td>
                                <td>
                                    <select class="form-control chosen-select employee" name="employee" id="employee">
                                        <option value="">Select Employee</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}" @if ($staff->id == $customer->employee_id) selected @endif>
                                                {{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Missing Amount </b></td>
                                <td><input type="number" class="form-control missing_amount" name="missing_amount"
                                        value="{{ $customer->missing_amount }}" onkeyup="calculate()"
                                        placeholder="Missing Amount"></td>
                            </tr>
                            <tr>
                                <td><b class="font-weight-bold">Remarks </b></td>
                                <td><textarea class="form-control" name="remarks" placeholder="Remarks">{{ $customer->remarks }}</textarea></td>
                            </tr>

                            <input type="hidden" class="form-control" name="customer_code"
                                value="{{ @$customer->customer->code }}" readonly>
                            <input type="hidden" class="form-control" name="collection" value="{{ $collections }}"
                                readonly>
                            <input type="hidden" class="form-control" name="late_days" value="{{ $duration }}"
                                readonly>
                            <input type="hidden" class="form-control" name="late_fee" value="{{ $late_fee }}"
                                readonly>
                            <input type="hidden" class="form-control total_due" value="{{ $total_due }}" readonly>
                            <input type="hidden" class="form-control outstanding_amount" value="{{ $outstanding }}"
                                readonly>
                            <input type="hidden" class="form-control cus_id" name="cus_id"
                                value="{{ $customer->customer_id }}" readonly>
                            <input type="hidden" class="form-control sale_id" name="sale_id"
                                value="{{ $customer->sale_id }}" readonly>
                            <input type="hidden" class="form-control invoice_no" name="invoice_no"
                                value="{{ $customer->invoice_no }}" readonly>
                            <input type="hidden" class="form-control schedule_value" name="schedule_value"
                                value="{{ $customer->is_schedule }}" readonly>
                            {{-- <input type="hidden" class="form-control settle_value" name="settle_value"
                                value="{{ $customer->is_settle }}" readonly> --}}
                            <input type="hidden" class="form-control" name="id" value="{{ $customer->id }}" readonly>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        function calculate() {
            var interest_receive = $(".interest_receive").val();
            var discount_amount = $(".discount_amount").val();
            var missing_amount = $(".missing_amount").val();
            var outstanding_amount = $(".outstanding_amount").val();
            var main_due = $(".main_due").val();
            if (isNaN(interest_receive)) {
                var interest_receive = 0;
            }

            if (isNaN(discount_amount)) {
                var discount_amount = 0;
            }

            if (isNaN(missing_amount)) {
                var missing_amount = 0;
            }

            var total = parseInt(outstanding_amount) + parseInt(interest_receive) - parseInt(discount_amount) - parseInt(missing_amount);

            $('.cam_due').val(total);

            if (isNaN(total)) {
                $('.cam_due').val(total_due);
            }

        }
    </script>

    <script>
        function schedule(customerId) {
            var checked = $('.on_schedule').is(":checked");
            if (checked == true) {
                var value = 1;
                $('.schedule_value').val(1);
            }
            if (checked != true) {
                var value = 0;
                $('.schedule_value').val(0);
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('closeAccount.lateDays') }}",
                data: {
                    customer_id: customerId,
                    schedule: value,
                },
                success: function(response) {
                    $('.late_days').html(response.late_days);
                },
                error: function(response) {}
            });
        }

        // function settle() {
        //     var checked = $('.settle').is(":checked");
        //     if (checked == true) {
        //         $('.late_fee').html(0);
        //         $('.settle_value').val(1);
        //         $('.main_due').val(0);
        //         $(".interest_receive").val(0);
        //         $(".discount_amount").val(0);
        //         $(".missing_amount").val(0);
        //         calculate();
        //     }
        //     if (checked != true) {
        //         var days = parseInt($('.late_days').html());
        //         var price = parseInt($('.sale_price').html());

        //         var type = $('.sale_type').html();
        //         var sale_type = type.replace(': ', '');

        //         $('.settle_value').val(0);

        //         var total = price * 0.12;
        //         var fee = total / 365;
        //         if (sale_type == 'Long Installment ') {
        //             var late_fee = Math.round(fee * days);
        //         } else {
        //             var late_fee = Math.round(fee * (days + 183));
        //         }
        //         if (late_fee < 0) {
        //             var late_fee = 0;
        //         }
        //         $('.late_fee').html(late_fee);


        //         var outstanding_amount = parseInt($('.outstanding_amount').val());
        //         var total_due = outstanding_amount + late_fee;
        //         $('.main_due').val(late_fee);
        //         $(".interest_receive").val(0);
        //         $(".discount_amount").val(0);
        //         $(".missing_amount").val(0);
        //         if (outstanding_amount < 0) {
        //             $(".interest_receive").val(-(outstanding_amount));
        //             $('.main_due').val(total_due);
        //         }
        //         calculate();
        //     }
        // }
    </script>

    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$customer->project_id }}');
    </script>
@endsection
