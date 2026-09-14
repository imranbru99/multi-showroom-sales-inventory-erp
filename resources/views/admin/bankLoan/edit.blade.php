@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>
<input type="hidden" name="id" value="{{ $bankLoan->id }}">
<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                <label for="code-prefix">Loan Date</label>
                <input type="text" class="form-control datepicker" name="loan_date" value="{{ date('d-m-Y', strtotime($bankLoan->loan_date)) }}" required>
                @if ($errors->has('loan_date'))
                @foreach ($errors->get('loan_date') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group {{ $errors->has('bank_name') ? ' has-danger' : '' }}">
                <label for="code-prefix">Bank Name</label>
                <input type="text" class="form-control" name="bank_name" value="{{ $bankLoan->bank_name }}" required>
                @if ($errors->has('bank_name'))
                @foreach ($errors->get('bank_name') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('loan_no') ? ' has-danger' : '' }}">
                <label for="code-prefix">Loan No</label>
                <input type="text" class="form-control" name="loan_no" id="loan_no" value="{{ $bankLoan->loan_no }}" required>
                @if ($errors->has('loan_no'))
                @foreach ($errors->get('loan_no') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('loan_amount') ? ' has-danger' : '' }}">
                <label for="code-prefix">Loan Amount</label>
                <input type="text" class="form-control" name="loan_amount" id="loan_amount" value="{{ $bankLoan->loan_amount }}" required>
                @if ($errors->has('loan_amount'))
                @foreach ($errors->get('loan_amount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('interest') ? ' has-danger' : '' }}">
                <label for="code-prefix">Interest (%)</label>
                <input type="text" class="form-control" name="interest" id="interest" value="{{ $bankLoan->interest }}" required>
                @if ($errors->has('interest'))
                @foreach ($errors->get('interest') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('total_amount') ? ' has-danger' : '' }}">
                <label for="code-prefix">Total Amount</label>
                <input type="text" class="form-control" name="total_amount" id="total_amount" value="{{ $bankLoan->total_amount }}" required>
                @if ($errors->has('total_amount'))
                @foreach ($errors->get('total_amount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('total_installment') ? ' has-danger' : '' }}">
                <label for="code-prefix">Total Installment</label>
                <input type="text" class="form-control" name="total_installment" id="total_installment" value="{{ $bankLoan->total_installment }}" required>
                @if ($errors->has('total_installment'))
                @foreach ($errors->get('total_installment') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('insatllment_amount') ? ' has-danger' : '' }}">
                <label for="code-prefix">Installment Amount</label>
                <input type="text" class="form-control" name="insatllment_amount" id="insatllment_amount" value="{{ $bankLoan->insatllment_amount }}" required>
                @if ($errors->has('insatllment_amount'))
                @foreach ($errors->get('insatllment_amount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row mt-5 d-flex justify-content-center">
    <div class="form-group col-md-3">
        <label for="start_date">Installment Start</label>
        <input type="month" class="form-control mt-1" id="start_date" name="start_date" value="{{ date('Y-m', strtotime(@$bankLoan->schedules[0]->date)) }}">
    </div>
    <div class="form-group col-md-2">
        <span class="btn btn-outline-info mt-4" onclick="Load()">Load</span>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bankLoan->schedules as $schedule)
                            <tr>
                                <td>{{$loop->iteration }}</td>
                                <td><input type="text" class="form-control" name="schedule_date[]" value="{{ date('m-Y', strtotime($schedule->date)) }}" readonly></td>
                                <td><input type="number" class="form-control text-right" name="schedule_amount[]" value="{{ $schedule->amount }}" readonly></td>
                            </tr>
                            @endforeach
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
    function calculatePayableAmount() {
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
        var amount = $('#total_amount').val();
        if (isNaN(amount)) {
            amount = 0;
        }
        var total_installment = $('#total_installment').val();

        if (amount <= 0) {
            swal({
                title: "Total Payable Amount need greater than 0"
                , type: "warning"
            });
            return;
        }
        if (total_installment <= 0) {
            $('#insatllment_amount').val(amount);
        }

        if (amount > 0 && total_installment > 0) {
            var payableAmount = amount / total_installment;
            $('#insatllment_amount').val(payableAmount);
        }


    }

    function Load() {
        var start_date = $('#start_date').val();
        var total_installment = $('#total_installment').val();
        var amount = $('#total_amount').val();


        if (amount <= 0) {
            swal({
                title: "Total Payable Amount need greater than 0"
                , type: "warning"
            });
            return;
        }
        if (total_installment <= 0) {
            swal({
                title: "Minimum Installment 1"
                , type: "warning"
            });
            return;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "get"
            , url: "{{ route('bankLoan.schedule') }}"
            , data: {
                date: start_date
                , qty: total_installment
                , amount: amount
            , }
            , success: function(response) {
                $('#schedule tbody tr').remove();
                if (response.length > 0) {
                    response.forEach(function(item, index) {
                        var tr = `
                                    <tr>
                                        <td>${item.sl}</td>
                                        <td><input type="text" class="form-control" name="schedule_date[]" value="${item.date}" readonly></td>
                                        <td><input type="number" class="form-control text-right" name="schedule_amount[]" value="${item.amount}" readonly></td>
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

</script>
@endsection
