@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        #total-target {
            vertical-align: middle;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ date('d-m-Y') }}"
                        placeholder="Invoice No">
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="invoice_no">Gatepass No</label>
                    <input type="text" name="invoice_no" class="form-control" value="{{ $issueNo }}"
                        placeholder="Invoice No">
                </div>
            </div>



            <div class="col-md-6">
                <label for="issue_id">Dealer</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="dealer_id" name="dealer_id">
                        <option value="">Select Dealer</option>
                        @foreach ($dealers as $dealer)
                            <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <div id="titleI">
            <h2></h2>
        </div>
        <div class="row" id="issueList">
          
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $('#dealer_id').change(function() {
            var dealer = $(this).val();
            $('#titleI').html('');
            $('#issueList').html('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: '{{ route('gatePass.getInvoices') }}',
                data: {
                    dealer_id: dealer
                },
                success: function(data) {
                    $('#titleI').html(`<h2>Invoice List</h2>`);
                    if (data.length > 0) {
                        data.forEach(function(item, index) {
                            var row = `
                                <div class="col-md-2">
                                    <input type="checkbox" name="issues[]" value="${item.id}"> ${item.issue_no}
                                </div>
                                `;
                            $('#issueList').append(row);
                        });
                    }
                }
            });
        });
    </script>
@endsection
