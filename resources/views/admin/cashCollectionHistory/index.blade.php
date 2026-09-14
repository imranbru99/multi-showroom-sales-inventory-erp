@extends('admin.layouts.master')

@section('content')
    @php
    use App\InvoiceSetup;
    use App\CustomerRegistrationSetup;
    use App\Product;
    @endphp

    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST"
        enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4 class="card-title">
                            {{ $title }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="print" value="print">
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-3">
                        <label for="from-date">From Date</label>
                        <input type="text" class="form-control {{ $print == 'print' ? 'datepicker' : 'add_datepicker' }}"
                            name="fromDate" placeholder="Select Date From"
                            value="{{ Date('d-m-Y', strtotime(@$fromDate)) }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label for="to-date">To Date</label>
                        <input type="text" class="form-control {{ $print == 'print' ? 'datepicker' : 'add_datepicker' }}"
                            name="toDate" placeholder="Select Date To" value="{{ Date('d-m-Y', strtotime(@$toDate)) }}"
                            readonly>
                    </div>

                    <div class="col-md-3">
                        <label for="collector">Collection By</label>
                        <div class="form-group">
                            <select class="form-control chosen-select" id="collector" name="collector[]" multiple>
                                @foreach ($collectorList as $collector)
                                    @php
                                        $select = '';
                                        if (@$collectorParameter) {
                                            if (in_array($collector->id, $collectorParameter)) {
                                                $select = 'selected';
                                            } else {
                                                $select = '';
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $collector->id }}" {{ $select }}>{{ $collector->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <label for="invoice_no">Invoice No</label>
                        <input type="text" class="form-control" name="invoice_no" placeholder="Invoice No"
                            value="{{ @$invoice_no }}">
                    </div>

                </div>

            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnVal"
                            value="Summary"><i class="fa fa-search"></i> Collection Summary</button>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnVal"
                            value="History"><i class="fa fa-search"></i> Collection History</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <form class="form-horizontal" id="search" action="{{ route($printFormLink) }}" method="POST" target="_blank">
        {{ csrf_field() }}

        @if (@$btnVal)
            <input type="hidden" name="btnVal" value="{{ @$btnVal }}">
        @endif

        @if (@$fromDate)
            <input type="hidden" name="fromDate" value="{{ @$fromDate }}">
        @endif

        @if (@$toDate)
            <input type="hidden" name="toDate" value="{{ @$toDate }}">
        @endif
        @if (@$toDate)
            <input type="hidden" name="invoice_no" value="{{ @$invoice_no }}">
        @endif
        @if (@$collectorParameter)
            @foreach ($collectorParameter as $collector)
                <input type="hidden" name="collector[]" value="{{ $collector }}">
            @endforeach
        @endif


        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="print"><i
                                class="fa fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
            <div class="card-body">

                @if ($btnVal == 'History')

                    <table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th class="text-nowrap" width="115px" style="text-align: center;">Collection Date</th>
                                <th width="180px">Client Name</th>
                                <th width="180px">Account No</th>
                                <th width="150px">Phone No</th>
                                <th width="80px">Collector</th>
                                <th class="text-center">MR No</th>
                                <th class="text-nowrap" width="130px" style="text-align: right;">Collection Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 0;
                            @endphp
                            @foreach ($cashCollectionHistoryList as $collectionHistory)
                                {{-- {{ dd($collectionHistory) }} --}}
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td style="text-align: center;">
                                        {{ date('d-m-Y', strtotime($collectionHistory->installment_collection_date)) }}
                                    </td>
                                    <td>{{ @$collectionHistory->collection->customer->name }}</td>
                                    <td>{{ @$collectionHistory->collection->customer->code }}</td>
                                    <td>{{ @$collectionHistory->collection->customer->phone_no }}</td>
                                    <td>{{ @$collectionHistory->collection->collector->name }}</td>
                                    <td class="text-center">{{ $collectionHistory->invoice_no }}
                                    <td style="text-align: right;">{{ $collectionHistory->installment_schedule_amount }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @endif

                @if ($btnVal == 'Summary')

                    <table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th width="180px">Client Name</th>
                                <th width="130px" style="text-align: right;">Collection Amount</th>
                                <th width="130px" style="text-align: right;">Agreement Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 0;
                            @endphp
                            @foreach ($cashCollectionHistoryList as $customer => $collectionHistory)
                                @php
                                    $cusData = \App\CustomerRegistrationSetup::select('tbl_customers.*', 'customer_agreement.agreement_amount')
                                        ->leftjoin('customer_agreement', 'customer_agreement.customer_id', '=', 'tbl_customers.id')
                                        ->where('tbl_customers.id', @$customer)
                                        ->first();
                                @endphp
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ @$cusData['name'] }}</td>
                                    <td style="text-align: right;">
                                        {{ $collectionHistory->sum('installment_schedule_amount') }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ @$cusData['agreement_amount'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @endif

            </div>
        </div>

    </form>

@endsection
