@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single {
            height: 35px !important;
        }

    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="dealer_id">Dealer</label>
                    <select class="form-control chosen-select" name="dealer_id" id="dealer_id">
                        <option value="">Select Dealer</option>
                        @foreach ($dealers as $dealers)
                            <option value="{{ $dealers->id }}" @if ($dealers->id == $dealerTransfer->dealer_id) selected @endif>{{ $dealers->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="transfer_from">Transfer From</label>
                <div class="form-group">
                    <select class="form-control chosen-select transfer_from" name="transfer_from" id="transfer_from">
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @if ($employee->id == $dealerTransfer->transfer_from) selected @endif>{{ $employee->name }}
                                ({{ $employee->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="transfer_to">Transfer To</label>
                <div class="form-group">
                    <select class="form-control chosen-select transfer_to" name="transfer_to" id="transfer_to">
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @if ($employee->id == $dealerTransfer->transfer_to) selected @endif>{{ $employee->name }}
                                ({{ $employee->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="date">Transfer Date</label>
                <div class="form-group">
                    <input type="text" class="form-control datepicker" name="date"
                        value="{{ date('d-m-Y', strtotime($dealerTransfer->transfer_date)) }}">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')


    <script>
        $(document).on('change', '#transfer_from', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $('#transfer_from').val();

            if (id != '') {
                $.ajax({
                    type: 'post',
                    url: '{{ route('dealerTransfer.getEmployee') }}',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $('#transfer_to option').remove();
                        $('#transfer_to').append('<option value="">Select Employee</option>');

                        if (data.length > 0) {
                            data.forEach(function(item, index) {
                                var option = '<option value="' + item.id +
                                    '">' + item.name + '(' + item.code + ')' + '</option>';
                                $('#transfer_to').append(option);
                            });


                            $('.chosen-select').chosen();
                            $('.chosen-select').trigger("chosen:updated");
                        }


                    }
                });
            }
        });
    </script>




@endsection
