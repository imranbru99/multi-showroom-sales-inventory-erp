@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('staff_id') ? ' has-danger' : '' }}">
                <label for="staff_id">Staff Name</label>
                <select class="form-control chosen-select" name="staff_id" id="staff">
                    <option value="">Select Staff</option>
                    @foreach($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->code }})</option>
                    @endforeach
                </select>
                @if ($errors->has('staff_id'))
                @foreach ($errors->get('staff_id') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                <label for="date">Date</label>
                <input type="text" class="form-control datepicker" name="date" value="{{ date('d-m-Y') }}" maxlength="20" required>
                @if ($errors->has('date'))
                @foreach ($errors->get('date') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type">
                    <option value="">Select Type</option>
                    <option value="Advance">Advance</option>
                    <option value="Loan">Loan</option>
                </select>
                @if ($errors->has('type'))
                @foreach ($errors->get('type') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('total_installment') ? ' has-danger' : '' }}">
                <label for="total_installment">Total Installment Qty</label>
                <input type="number" class="form-control" id="total_installment" name="total_installment" value="1">
                @if ($errors->has('total_installment'))
                @foreach ($errors->get('total_installment') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('amount') ? ' has-danger' : '' }}">
                <label for="amount">Loan/Advance Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" value="0">
                @if ($errors->has('amount'))
                @foreach ($errors->get('amount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

    </div>

    <div class="row mt-5 d-flex justify-content-center">
        <div class="form-group col-md-3">
            <label for="start_date">Installment Start</label>
            <input type="month" class="form-control mt-1" id="start_date" name="start_date" value="{{ date('Y-m') }}">
        </div>
        <div class="form-group col-md-2">
            <span class="btn btn-outline-info mt-4" onclick="Load()">Load</span>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="schedule">
        <thead>
            <tr class="bg-success text-center">
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div>
        @endsection
        @section('custom-js')
        <script>
            function Load() {
                var start_date = $('#start_date').val();
                var total_installment = $('#total_installment').val();
                var amount = $('#amount').val();
                var type = $('#type').val();
                var staff = $('#staff').val();


                
                if (staff == '') {
                    swal({
                        title: "Select ftaff"
                        , type: "warning"
                    });
                    return;
                }
                if (type == '') {
                    swal({
                        title: "Select Type"
                        , type: "warning"
                    });
                    return;
                }
                if (amount <= 0) {
                    swal({
                        title: "Amount need greater than 0"
                        , type: "warning"
                    });
                    return;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    , type: "get"
                    , url: "{{ route('staffLoan.schedule') }}"
                    , data: {
                        date: start_date
                        , qty: total_installment
                        , amount: amount
                        , type: type
                    , }
                    , success: function(response) {
                        $('#schedule tbody tr').remove();
                        if (response.length > 0) {
                            response.forEach(function(item, index) {
                                var tr = `
                                    <tr>
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
