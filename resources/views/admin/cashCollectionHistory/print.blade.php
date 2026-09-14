@extends('admin.layouts.masterPrint')

@section('content')
    @php
    use App\InvoiceSetup;
    use App\CustomerRegistrationSetup;
    use App\Product;
    @endphp

    @if ($btnVal == 'History')
        <table id="report-table">
            <caption>
                {{ @$title }}
                @if (!empty($staffs))
                    @if ($staffs->count())
                        Sales By @foreach ($staffs as $staff)
                            {{ $staff->name }},
                        @endforeach
                    @endif
                @endif
            </caption>
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th width="115px" style="text-align: center;">Date</th>
                    <th width="100px">A/C No</th>
                    <th width="150px">Client Name</th>
                    <th width="90px">Phone No</th>
                    <th width="80px">Collect By</th>
                    <th width="130px" style="text-align: right;">Amount</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $totalCollectionAmount = 0;
                @endphp
                @foreach ($cashCollectionHistoryList as $collectionHistory)
                    @php
                        $totalCollectionAmount += $collectionHistory->installment_schedule_amount;
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td style="text-align: center;">
                            {{ date('d-m-Y', strtotime($collectionHistory->installment_collection_date)) }}</td>
                        <td>{{ @$collectionHistory->collection->customer->code }}</td>
                        <td>{{ @$collectionHistory->collection->customer->name }}</td>
                        <td>{{ @$collectionHistory->collection->customer->phone_no }}</td>
                        <td>{{ @$collectionHistory->collection->sale->seller->name }}</td>
                        <td style="text-align: right;">{{ $collectionHistory->installment_schedule_amount }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="6">Total</th>
                    <th style="text-align: right;">{{ number_format($totalCollectionAmount, '2') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($btnVal == 'Summary')
        <table id="report-table">
            <caption>
                {{ @$title }}

                @if (!empty($staffs))
                    @if ($staffs->count())
                        Sales By @foreach ($staffs as $staff)
                            {{ $staff->name }},
                        @endforeach
                    @endif
                @endif
            </caption>
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th width="180px">Client Name</th>
                    <th style="text-align: right;">Collection Amount</th>
                    <th align="right">Agreement Amount</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $totalCollectionAmount = 0;
                    $totalAgreeAmount = 0;
                @endphp
                @foreach ($cashCollectionHistoryList as $customer => $collectionHistory)
                    @php
                        $totalCollectionAmount += $collectionHistory->sum('installment_schedule_amount');
                        
                        $cusData = \App\CustomerRegistrationSetup::select('tbl_customers.*', 'customer_agreement.agreement_amount')
                            ->leftjoin('customer_agreement', 'customer_agreement.customer_id', '=', 'tbl_customers.id')
                            ->where('tbl_customers.id', @$customer)
                            ->first();
                        
                        $totalAgreeAmount += @$cusData['agreement_amount'];
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ @$cusData['name'] }}</td>
                        <td style="text-align: right;">{{ $collectionHistory->sum('installment_schedule_amount') }}</td>
                        <td style="text-align: right;">{{ @$cusData['agreement_amount'] }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th style="text-align: right;">{{ number_format($totalCollectionAmount, '2') }}</th>
                    <th style="text-align: right;">{{ number_format($totalAgreeAmount, '2') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

@endsection
