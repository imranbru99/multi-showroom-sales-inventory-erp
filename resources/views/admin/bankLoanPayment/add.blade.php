@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('payment_date') ? ' has-danger' : '' }}">
                <label for="code-prefix">Payment Date</label>
                <input type="text" class="form-control datepicker" name="payment_date" value="{{ date('d-m-Y') }}" required>
                @if ($errors->has('payment_date'))
                @foreach ($errors->get('payment_date') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('loan_no') ? ' has-danger' : '' }}">
                <label for="code-prefix">Loan No</label>
                <select class="form-control chosen-select" name="loan_id" onchange="loanSchedules()" id="loan">
                    <option value="">Select Loan No</option>
                    @foreach($loans as $loan)
                    <option value="{{ $loan->id }}">{{ $loan->loan_no }} ({{$loan->bank_name}})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('payment_amount') ? ' has-danger' : '' }}">
                <label for="code-prefix">Total Payment Amount</label>
                <input type="text" class="form-control" name="payment_amount" id="payment_amount" value="0" required>
                @if ($errors->has('payment_amount'))
                @foreach ($errors->get('payment_amount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="schedule">
                        <thead>
                            <tr class="bg-success text-center">
                                <th width="5%">SL</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    function calculatePaymentAmount() {
        var loanAmount = parseInt($('#loan_amount').val());
        var interest = parseInt($('#interest').val());

        if (isNaN(loanAmount)) {
            loanAmount = 0;
        }
        if (isNaN(interest)) {
            interest = 0;
        }

        if (loanAmount == 0) {
            $('#total_amount').val(0);
        } else {
            if (interest == 0) {
                $('#total_amount').val(loanAmount);
            } else {
                var totalPay = loanAmount + (loanAmount * (interest / 100));
                $('#total_amount').val(totalPay);
            }
        }

    }

    function divideInstallmentAmount() {

    }

    function loanSchedules() {
        var loan = $('#loan').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "get"
            , url: "{{ route('bankLoanPayment.schedule') }}"
            , data: {
                loan: loan
            , }
            , success: function(response) {
                $('#schedule tbody tr').remove();
                if (response.length > 0) {
                    var sl = 1;
                    response.forEach(function(item, index) {
                        var tr = `
                                    <tr>
                                        <td>${sl++}</td>
                                        <td><input type="text" class="form-control" name="schedule_date[]" value="${item.date}" readonly></td>
                                        <td><input type="number" class="form-control text-right" id="schedule_amount_${item.id}" name="schedule_amount[]" value="${item.amount}" readonly></td>
                                        <td><input type="checkbox" class="form-control" id="check_${item.id}" name="schedule_id[]" value="${item.id}" onclick="checkValue(${item.id});"></td>
                                    </tr>
                                `;
                        $('#schedule tbody').append(tr);
                    });
                }
            }
            , error: function(response) {
                error = "Failed.";
                swal({
                    title: "<small class='text-danger'>Error!</small>"
                    , type: "error"
                    , text: error
                    , timer: 2000
                    , html: true
                , });
            }
        });
    }

    function checkValue(id) {
        var checked = $('#check_' + id).is(":checked");
        var amount = parseInt($('#schedule_amount_' + id).val());
        var totalAmount = parseInt($('#payment_amount').val());
        if (checked == true) {
            $('#payment_amount').val((totalAmount + amount));
        }
        if (checked != true) {
            $('#payment_amount').val((totalAmount - amount));
        }
    }

</script>
@endsection
