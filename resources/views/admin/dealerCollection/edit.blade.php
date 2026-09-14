@extends('admin.layouts.masterAddEdit')

@section('card_body')
    @php
    use App\DealerCollection;
    $lastDealerCollectionId = DealerCollection::max('id');
    if (@$lastDealerCollectionId) {
        $paymentNo = 100000000 + $lastDealerCollectionId + 1;
    } else {
        $paymentNo = 100000000 + 1;
    }
    @endphp

    <style>
        th {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
        }

    </style>
    <div class="card-body">
        <div class="row">
            <input type="hidden" name="collection_id" value="{{ $dealerCollection->id }}">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="dealer">Dealer</label>
                    <div class="form-group">
                        <select class="form-control " id="dealer" name="dealer" readonly>
                            <option value="" disabled>Select Dealer</option>
                            @foreach ($dealers as $dealer)
                                <option value="{{ $dealer->id }}" @if ($dealer->id == $dealerCollection->dealer_id) selected @else disabled @endif>{{ $dealer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label for="phone">Payment No</label>
                <div class="form-group {{ $errors->has('paymentNo') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" id="paymentNo" name="paymentNo"
                        value="{{ $dealerCollection->payment_no }}" required readonly />
                    @if ($errors->has('paymentNo'))
                        @foreach ($errors->get('paymentNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <label for="payment-date">Payment Date</label>
                <div class="form-group {{ $errors->has('paymentDate') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control datepicker" id="paymentDate" name="paymentDate"
                        value="{{ date('d-m-Y' , strtotime($dealerCollection->payment_date)) }}" readonly>
                    @if ($errors->has('paymentDate'))
                        @foreach ($errors->get('paymentDate') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                @php
                    $types = ['Cash' => 'Cash', 'Bkash' => 'Bkash', 'Bank' => 'Bank', 'Others' => 'Others'];
                @endphp
                <label for="money-receipt-type">Money Receipt Type</label>
                <div class="form-group {{ $errors->has('moneyReceiptType') ? ' has-danger' : '' }}">
                    <div class="form-group">
                        <select class="form-control" id="moneyReceiptType" name="moneyReceiptType">
                            {{-- <option value="">Select Money Receipt Type</option> --}}
                            @foreach ($types as $key => $value)
                                <option value="{{ $key }}" @if ($key == $dealerCollection->money_receipt_type) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($errors->has('moneyReceiptType'))
                        @foreach ($errors->get('moneyReceiptType') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4" id="note">
                <label for="money-receipt-no">Note</label>
                <div class="form-group {{ $errors->has('note') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" id="note" name="note"
                        value="{{ $dealerCollection->note }}" />
                    @if ($errors->has('note'))
                        @foreach ($errors->get('note') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4" id="bank">
                <div class="form-group">
                    <label for="b">Banks</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="b" name="bank">
                            <option value="">Select Banks</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" @if ($bank->id == $dealerCollection->bank) selected @endif>
                                    {{ $bank->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @php
                $collection_remarks = ['Advance From Collection', 'collection'];
            @endphp

            <div class="col-md-4">
                <label for="payment_type">Payment Type</label>
                <div class="form-group {{ $errors->has('payment_type') ? ' has-danger' : '' }}">
                    <select name="payment_type" id="payment_type" class="form-control">
                        <option value="advance" @if ($dealerCollection->remarks == 'advance') selected @else disabled @endif>Advance</option>
                        <option value="adjust" @if ($dealerCollection->remarks == 'adjust') selected @else disabled @endif>Adjust</option>
                        <option value="collection" @if (in_array($dealerCollection->remarks, $collection_remarks)) selected @else disabled @endif>Collection</option>
                    </select>
                </div>
            </div>

        </div>


        <div class="row">

            <div class="col-md-4">
                <label for="remarks">Remarks</label>
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <textarea class="form-control" id="remarks" name="remarks"
                        rows="1">{{ $dealerCollection->remarks }}</textarea>
                    @if ($errors->has('remarks'))
                        @foreach ($errors->get('remarks') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <label for="sale_by">Collection By</label>
                <div class="form-group {{ $errors->has('sale_by') ? ' has-danger' : '' }}">
                    <select name="sale_by" class="form-control chosen-select" id="sale_by">
                        @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}" @if($staff->id == $dealerCollection->sale_by) selected @endif>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4" id="total">
                <label for="money-receipt-no">Total Collection</label>
                <div class="form-group {{ $errors->has('total') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" id="payment_amt" name="payment_amount"
                        value="{{ $dealerCollection->payment_amount }}" />
                    @if ($errors->has('total'))
                        @foreach ($errors->get('total') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


        </div>


        <div class="row" id="tableInfo">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL#</th>
                                <th>Invoice No</th>
                                <th>Product Name</th>
                                <th>Product Model</th>
                                <th>Sale Amount</th>
                                <th>Previous Collection</th>
                                <th>Current Collection</th>
                                <th>Due Amount</th>
                                <th>
                                    <input type="checkbox" name="selectAll" id="checkAll">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tbdy">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($dealerCollection->issueList as $issuel)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ @$issuel->issue->issue_no }}</td>
                                    <td>{{ @$issuel->product->name }}</td>
                                    <td>{{ @$issuel->product->model_no }}</td>
                                    <td>
                                        {{ $issuel->amount }}
                                        <input type="hidden" id="saleAmount" value="{{ $issuel->amount }}">
                                    </td>
                                    <td>
                                        0
                                        <input type="hidden" class="prev_collection" id="prev_collection[{{ $issuel->id }}]"
                                            value="0">
                                    </td>
                                    <td>
                                        <input type="number" step=".01" name="collection[{{ $issuel->id }}]"
                                            value="{{ $issuel->collection }}" class="form-control curr_collection"
                                            onkeyup="DueCalc(event)">
                                    </td>
                                    <td>
                                        <p class="due">{{ $issuel->amount - $issuel->collection }}</p>
                                        <input type="hidden" id="due" value="{{ $issuel->amount - $issuel->collection }}">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="id" id="id" name="id[]" value="{{ $issuel->id }}"
                                            onclick="singleCheckBoxChanged(event)" checked>
                                    </td>
                                </tr>
                            @endforeach

                                @foreach ($allProducts as $issuel)
                                    @php

                                        if ($issuel['collection_id'] > 0) {
                                            continue;
                                        }

                                        if ($issuel['saleAmount'] == $issuel['collection']) {
                                            continue;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ @$issuel['issue_no'] }}</td>
                                        <td>{{ @$issuel['productName'] }}</td>
                                        <td>{{ @$issuel['productModel'] }}</td>
                                        <td>
                                            {{ $issuel['saleAmount'] }}
                                            <input type="hidden" id="saleAmount" value="{{ $issuel['saleAmount'] }}">
                                        </td>
                                        <td>
                                            0
                                            <input type="hidden" class="prev_collection"
                                                id="prev_collection[{{ $issuel['saleListId'] }}]" value="0">
                                        </td>
                                        <td>
                                            <input type="number" step=".01" name="collection[{{ $issuel['saleListId'] }}]"
                                                value="{{ $issuel['collection'] }}" class="form-control curr_collection"
                                                onkeyup="DueCalc(event)">
                                        </td>
                                        <td>
                                            <p class="due">{{ $issuel['saleAmount'] - $issuel['collection'] }}</p>
                                            <input type="hidden" id="due"
                                                value="{{ $issuel['saleAmount'] - $issuel['collection'] }}">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="id" id="id" name="id[]" value="{{ $issuel['saleListId'] }}"
                                                onclick="singleCheckBoxChanged(event)" checked>
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>

@endsection

@section('custom-js')
    <script type="text/javascript">
        // $('#remarks').val('advance');
        $('#bank').hide();
        $('#note').show();

        $('#payment_type').change(function(e) {
            e.preventDefault();
            let payment_type = $('#payment_type').val();
            let remarks = $('#remarks');

            remarks.val(payment_type);

            if (payment_type == 'advance') {
                $('#tbdy').html('');
            } else {
                fetchData();
            }

            if (payment_type == 'adjust') {
                var dealer_id = $('#dealer option:selected').val();
                getDealerAdvanceAmount(dealer_id);
            }

        });

        $('#payment_amt').keypress(function(e) {

            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == '13') {
                e.preventDefault();

                $('.curr_collection').each(function() {

                    let td = $(this).parent().parent();

                    $(this).val(0);


                    let saleAmount = +td.find('#saleAmount').val();
                    $(td).find('#due').val(saleAmount);
                });

                let totalAmount = parseFloat($(e.target).val());

                $('.curr_collection').each(function() {


                    if (totalAmount > 0) {

                        let tr = $(this).parent().parent();
                        // console.log(tr);

                        let due = $(tr).find('#due').val();
                        due = Number.parseFloat(due).toFixed(2);
                        due = parseFloat(due);

                        if (due > totalAmount) {
                            let currentCollection = tr.find('.curr_collection').val(Number.parseFloat(
                                totalAmount).toFixed(2));
                            $(tr).find('#id').prop('checked', true);
                            let finalDue = parseFloat($(tr).find('#due').val()) - totalAmount;
                            $(tr).find('#due').val(finalDue);
                            $(tr).find('.due').text(finalDue);
                            totalAmount -= totalAmount;
                        } else {
                            let currentCollection = tr.find('.curr_collection').val(due);
                            totalAmount -= due;
                            $(tr).find('#id').prop('checked', true);
                            $(tr).find('#due').val($(tr).find('#due').val() - due);
                            $(tr).find('.due').text($(tr).find('#due').val());
                        }

                    }

                });

            }
        });

        function reCalculateTotalAmount() {
            let totalCollection = 0;

            // update total Collection
            let AllCheckedCheckbox = $('.id:checkbox:checked');

            AllCheckedCheckbox.each(function() {
                let tr = $(this).parent().parent();

                let currentCollection = tr.find('.curr_collection').val();

                totalCollection += +currentCollection;
            });


            $('#payment_amt').val(totalCollection);

        }

        function singleCheckBoxChanged(e) {

            let td = e.target.parentNode.parentNode;

            let checkbox = +$(e.target).prop("checked");

            if (checkbox) {
                let due = $(td).find('#due').val();
                $(td).find('.curr_collection').val(due);
            }

            reCalculateTotalAmount();
        }

        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);

            // update total Collection
            let AllCheckedCheckbox = $('.id:checkbox:checked');

            AllCheckedCheckbox.each(function() {
                let td = $(this).parent().parent();
                let due = $(td).find('#due').val();
                $(td).find('.curr_collection').val(due);
            });

            reCalculateTotalAmount();
        });


        function DueCalc(e) {
            let td = e.target.parentNode.parentNode;

            let currentCollection = +$(e.target).val();
            let previousCollection = +$(td.querySelectorAll(".prev_collection")).val();
            let saleAmount = +$(td.querySelectorAll("#saleAmount")).val();

            let total = saleAmount - (previousCollection + currentCollection);

            $(td.querySelectorAll(".due")).text(total);
            $(td.querySelectorAll("#due")).val(total);

            // update total Collection
            let checkboxCondition = $(td.querySelectorAll("#id")).prop("checked");
            if (checkboxCondition) {
                reCalculateTotalAmount();
            }
        }


        $('#moneyReceiptType').change(function(e) {
            e.preventDefault();

            if ($('#moneyReceiptType').val() == "Bank") {

                $('#bank').show();
                $('#note').hide();

            } else {

                $('#note').show();
                $('#bank').hide();
            }
        });


        function getDealerAdvanceAmount(dealer_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                type: 'post',
                url: '{{ route('dealer.adjust.amount') }}',
                data: {
                    dealer_id: dealer_id
                },
                success: function(data) {
                    $('#payment_amt').val(data);
                    console.log(data);
                }
            });
        }

        function fetchData() {

            if ($('#payment_type').val() == 'advance') {
                $('#tbdy').html('');
                return false;
            }

            if ($('#payment_type').val() == 'adjust') {
                var dealer_id = $('#dealer option:selected').val();
                getDealerAdvanceAmount(dealer_id);
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var dealerId = $('#dealer option:selected').val();

            $.ajax({
                type: 'post',
                url: '{{ route('dealerCollection.getAllProductInfo') }}',
                data: {
                    dealerId: dealerId
                },
                success: function(data) {
                    $('#tbdy').html('');

                    let i = 1;

                    for (const issuelist in data.issue) {
                        if (Object.hasOwnProperty.call(data.issue, issuelist)) {
                            const issueList = data.issue[issuelist];

                            // console.log(issueList);

                            for (const product in issueList.products) {
                                if (Object.hasOwnProperty.call(issueList.products, product)) {
                                    const p = issueList.products[product];

                                    if (parseFloat(p.collection) >= parseFloat(p.amount)) {
                                        continue;
                                    }

                                    let tr = `
                                                            <tr>
                                                                <td>${i}</td>
                                                                <td>${issueList.issue_no}</td>
                                                                <td>${p.product.name}</td>
                                                                <td>${p.product.model_no}</td>
                                                                <td>
                                                                    ${p.amount}
                                                                    <input type="hidden" id="saleAmount" value="${p.amount}">
                                                                </td>
                                                                <td>
                                                                    ${p.collection}
                                                                    <input type="hidden" class="prev_collection" id="prev_collection[${p.id}]" value="${p.collection}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step=".01" value="0" name="collection[${p.id}]" class="form-control curr_collection" onkeyup="DueCalc(event)">
                                                                </td>
                                                                <td>
                                                                    <p class="due">${(p.amount - p.collection).toFixed(2)}</p>
                                                                    <input type="hidden" id="due" value="${(p.amount - p.collection).toFixed(2)}">
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" class="id" id="id" name="id[]" value="${p.id}" onclick="singleCheckBoxChanged(event)">
                                                                </td>
                                                            </tr>`;

                                    $('#tbdy').append(tr);

                                    i++;
                                }
                            }

                        }
                    }

                    // $('#tbdy').append();
                }
            });
        }


        $(document).on('change', '#dealer', fetchData);


        $(document).on('change', '#productIssue', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var productIssueId = $('#productIssue option:selected').val();
            if (productIssueId != '') {
                $.ajax({
                    type: 'post',
                    url: '{{ route('dealerCollection.getDealerInfo') }}',
                    data: {
                        productIssueId: productIssueId
                    },
                    success: function(data) {
                        var productIssueList = data.productIssueList;
                        var dealerCollection = data.dealerCollection;

                        var dueAmount = productIssueList.total_amount - dealerCollection
                            .previousCollection;

                        $('#dueAmount').val(dueAmount);
                        $('#newPaid').val(0);
                        $('#balance').val(0);
                    }
                });
            } else {
                $('#dueAmount').val(0);
                $('#newPaid').val(0);
                $('#balance').val(0);
            }
        });

        function findBalance() {
            var newPaid = $("#newPaid").val();
            var dueAmount = $("#dueAmount").val();

            var balance = dueAmount - newPaid;

            $("#balance").val(balance);

        }

        /*end code for product info*/

        $("form").submit(function(e) {
            if ($('.currentDue').val() < 0) {
                alert('Collection amount sholuld not be cross invoice amount!');
                e.preventDefault();
            }
        });

    </script>
@endsection
