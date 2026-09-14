@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        .blockTitle {
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }

        .guarantorInfo {
            border: 1px solid #000;
            margin-top: 14px;
        }

        .cls {
            position: absolute;
            right: 22px;
        }

        .closeBtn:hover {
            cursor: pointer;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="row">

            <input type="hidden" name="collectionID" value="{{ $collection->id }}">

            <div class="col-md-2">
                <div class="form-group">
                    <label for="collection_date">Collection Date</label>
                    <input type="text" class="form-control datepicker" name="collection_date"
                        value="{{ date('d-m-Y', strtotime($collection->installment_collection_date)) }}">
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="account_no">Account No</label>
                    <select name="customer_id" class="form-control chosen-select">
                        <option value="">Select Account</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @if (@$collection->collection->customer->id == @$customer->id) selected @endif>{{ $customer->name }}
                                ({{ $customer->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="collector">Collection By</label>
                    <select name="collector" class="form-control chosen-select">
                        <option value="">Select Employee</option>
                        @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}" @if ($staff->id == @$collection->collection->reference_id) selected @endif>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="mr_no">MR. No</label>
                    <input type="text" class="form-control" name="mr_no" value="{{ $collection->invoice_no }}">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="nick-name">Collection Amount</label>
                    <input type="text" class="form-control" name="collection_amount"
                        value="{{ $collection->installment_schedule_amount }}">
                </div>
            </div>

        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$collection->project_id }}');
    </script>
@endsection
