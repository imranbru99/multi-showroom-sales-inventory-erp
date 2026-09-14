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
                    <select class="form-control" name="staff_id" id="staff" disabled>
                        <option value="">Select Staff</option>
                        @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}" @if ($staff->id == $collection->staff_id) selected @endif>{{ $staff->name }}
                                ({{ $staff->code }})</option>
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
                    <input type="text" class="form-control datepicker" name="date"
                        value="{{ date('d-m-Y', strtotime($collection->collection_date)) }}" required>
                    @if ($errors->has('date'))
                        @foreach ($errors->get('date') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('amount') ? ' has-danger' : '' }}">
                    <label for="amount">Collection Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount"
                        value="{{ $collection->amount }}">
                    @if ($errors->has('amount'))
                        @foreach ($errors->get('amount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>


        <table class="table table-bordered table-striped" id="schedule">
            <thead>
                <tr class="bg-success text-center">
                    <th>Date</th>
                    <th>Schedule Amount</th>
                    <th>Paid Amount</th>
                    <th>Collection Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection->collections as $coll)
                    <tr>
                        <td><input type="text" class="form-control" name="schedule_date[]"
                                value="{{ $coll->schedule_date }}" readonly></td>
                        <td><input type="number" class="form-control text-right"
                                id="schedule_amount_{{ $coll->schedule_id }}" value="{{ $coll->schedule->amount }}"
                                readonly></td>
                        <td><input type="number" class="form-control text-right" id="collected_{{ $coll->schedule_id }}"
                                value="{{ $coll->schedule->amount - $coll->collection_amount }}" readonly></td>
                        <td><input type="number" class="form-control text-right collection_amount"
                                name="collection_amount[]" id="collection_amount_{{ $coll->schedule_id }}"
                                oninput="collection({{ $coll->schedule_id }})" value="{{ $coll->collection_amount }}">
                        </td>
                        <td><input type="checkbox" class="form-control" id="check_{{ $coll->schedule_id }}"
                                name="schedule_id[]" value="{{ $coll->schedule_id }}"
                                onclick="checkValue({{ $coll->schedule_id }});" checked></td>
                    </tr>
                @endforeach
                @foreach ($colSchedules as $colSchedule)
                    @if ($colSchedule->amount > $colSchedule->collection_amount)
                        <tr>
                            <td><input type="text" class="form-control" name="schedule_date[]"
                                    value="{{ $colSchedule->date }}" readonly></td>
                            <td><input type="number" class="form-control text-right"
                                    id="schedule_amount_{{ $colSchedule->id }}"
                                    value="{{ $colSchedule->amount }}" readonly></td>
                            <td><input type="number" class="form-control text-right"
                                    id="collected_{{ $colSchedule->id }}"
                                    value="{{ $colSchedule->collection_amount }}" readonly></td>
                            <td><input type="number" class="form-control text-right collection_amount"
                                    name="collection_amount[]" id="collection_amount_{{ $colSchedule->id }}"
                                    oninput="collection({{ $colSchedule->id }})"
                                    value="{{ $colSchedule->collection_amount }}"></td>
                            <td><input type="checkbox" class="form-control" id="check_{{ $colSchedule->id }}"
                                    name="schedule_id[]" value="{{ $colSchedule->id }}"
                                    onclick="checkValue({{ $colSchedule->id }});"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div>
        @endsection

        @section('custom-js')
            <script>
                $('#staff').change(function() {
                    var staff = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: "{{ route('staffLoanCollection.schedule') }}",
                        data: {
                            staff: staff,
                        },
                        success: function(response) {
                            $('#schedule tbody tr').remove();
                            if (response.length > 0) {
                                response.forEach(function(item, index) {
                                    if (item.amount > item.collection_amount) {
                                        var tr = `
                                        <tr>
                                            <td><input type="text" class="form-control" name="schedule_date[]" value="${item.date}" readonly></td>
                                            <td><input type="number" class="form-control text-right" id="schedule_amount_${item.id}" value="${item.amount}" readonly></td>
                                            <td><input type="number" class="form-control text-right" id="collected_${item.id}" value="${item.collection_amount}" readonly></td>
                                            <td><input type="number" class="form-control text-right collection_amount" name="collection_amount[]" id="collection_amount_${item.id}" oninput="collection(${item.id})" value="0"></td>
                                            <td><input type="checkbox" class="form-control" id="check_${item.id}" name="schedule_id[]" value="${item.id}" onclick="checkValue(${item.id});"></td>
                                        </tr>
                                    `;
                                    }
                                    $('#schedule tbody').append(tr);
                                });
                            }
                        }
                    });
                });


                function checkValue(id) {
                    var checked = $('#check_' + id).is(":checked");
                    var schedule = parseInt($('#schedule_amount_' + id).val());
                    var collected = parseInt($('#collected_' + id).val());
                    var collection = parseInt($('#collection_amount_' + id).val());
                    var amount = schedule - collected;
                    if (checked == true) {
                        $('#collection_amount_' + id).val(amount);
                    }
                    if (checked != true) {
                        $('#collection_amount_' + id).val(0);
                    }

                    var totalAmount = 0;
                    $('.collection_amount').each(function() {
                        var amount = parseFloat($(this).val());
                        if (isNaN(amount)) {
                            amount = 0;
                        }
                        totalAmount += amount;
                    });
                    $('#amount').val(totalAmount);
                }

                function collection(id) {
                    var schedule = parseInt($('#schedule_amount_' + id).val());
                    var collected = parseInt($('#collected_' + id).val());
                    var collection = parseInt($('#collection_amount_' + id).val());
                    var amount = schedule - collected;

                    if ($('#collection_amount_' + id).val() > 0) {
                        $("#check_" + id).prop("checked", true);
                    } else {
                        $("#check_" + id).prop("checked", false);
                    }

                    var totalAmount = 0;
                    $('.collection_amount').each(function() {
                        var amount = parseFloat($(this).val());
                        if (isNaN(amount)) {
                            amount = 0;
                        }
                        totalAmount += amount;
                    });
                    $('#amount').val(totalAmount);
                }
            </script>
        @endsection
