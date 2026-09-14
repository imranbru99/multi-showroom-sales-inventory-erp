@extends('admin.layouts.masterPrint')

@section('content')

    <caption>Customer Statement On {{ date('d-m-Y', strtotime($fromDate)) }} To
        {{ date('d-m-Y', strtotime($toDate)) }}</caption>

    <table id="report-table">
        <tbody>
            <tr>
                <td colspan="2">Customer Name</td>
                <td colspan="2"> {{ $customerStatements[0]['customer_name'] }} </td>
                <td colspan="3">Customer Address</td>
                <td colspan="3">{{ $customerStatements[0]['customer_address'] }}</td>
            </tr>
            <tr>
                <td colspan="2">Customer Code</td>
                <td colspan="2"> {{ $customerStatements[0]['customer_code'] }} </td>
                <td colspan="3">Customer Phone</td>
                <td colspan="3">{{ $customerStatements[0]['customer_phone'] }}</td>
            </tr>
            <tr>
                <th colspan="9"
                    style="background-color: #778899; text-align: right; font-weight: bold; padding-right: 5px;">Previous
                    Balance</th>
                <td style="text-align: right;">{{ $previousBalance }}</td>
            </tr>
        </tbody>
    </table>


    <table id="report-table">

        <thead>
            {{-- <tr>
                <td colspan="2">Customer Name</td>
                <td colspan="2"> {{ $customerStatements[0]['customer_name'] }} </td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="2">Customer Code</td>
                <td colspan="2"> {{ $customerStatements[0]['customer_code'] }} </td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <th colspan="9"
                    style="background-color: #778899; text-align: right; font-weight: bold; padding-right: 5px;">Previous
                    Balance</th>
                <td style="text-align: right;">{{ $previousBalance }}</td>
            </tr> --}}
            <tr>
                <th width="20px">Sl</th>
                <th width="70px">Date</th>
                <th width="200px">Inv/MR No</th>
                <th width="70px">Sales</th>
                <th width="70px">Return</th>
                <th width="70px">Discount</th>
                <th width="70px">Gift Voucher</th>
                <th width="70px">Exchange CRT</th>
                <th width="80px">Collection</th>
                <th width="70px">Balance</th>
                <th width="70px">Agreement</th>
            </tr>
        </thead>

        <tbody>
            @php
                $totalSale = 0;
                $totalReturn = 0;
                $totalCollection = 0;
                $totalDiscount = 0;
                $totalGiftVoucher = 0;
                $totalExchangeCrt = 0;
                $totalAgreement = 0;
            @endphp
            @foreach ($customerStatements as $customerStatement)
                @php
                    $totalSale += $customerStatement['sale'];
                    $totalReturn += $customerStatement['return'];
                    $totalCollection += $customerStatement['collection'];
                    $totalDiscount += $customerStatement['discount'];
                    $totalGiftVoucher += $customerStatement['giftVoucher'];
                    $totalExchangeCrt += $customerStatement['exchangeCRT'];
                    $totalAgreement += $customerStatement['agreement'];
                @endphp
                <tr>
                    <td>{{ $customerStatement['sl'] }}</td>
                    <td>{{ $customerStatement['date'] }}</td>
                    <td>{{ $customerStatement['invoice_no'] }}</td>
                    <td class="align-right">{{ $customerStatement['sale'] }}</td>
                    <td class="align-right">{{ $customerStatement['return'] }}</td>
                    <td class="align-right">{{ $customerStatement['discount'] }}</td>
                    <td class="align-right">{{ $customerStatement['giftVoucher'] }}</td>
                    <td class="align-right">{{ $customerStatement['exchangeCRT'] }}</td>
                    <td class="align-right">{{ $customerStatement['collection'] }}</td>
                    <td class="align-right">{{ $customerStatement['due'] }}</td>
                    <td class="align-right">{{ $customerStatement['agreement'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <tfoot>
            <tr>
                <th style="text-align: right;"><b>Total Sales : </b></th>
                <td style="text-align: right;">{{ round($totalSale, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Return : </b></th>
                <td style="text-align: right;">{{ round($totalReturn, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Collection : </b></th>
                <td style="text-align: right;">{{ round($totalCollection, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Discount : </b></th>
                <td style="text-align: right;">{{ round($totalDiscount, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Gift Voucher : </b></th>
                <td style="text-align: right;">{{ round($totalGiftVoucher, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Exchange CRT : </b></th>
                <td style="text-align: right;">{{ round($totalExchangeCrt, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Balance : </b></th>
                <td style="text-align: right;">{{ round($customerStatement['due'], 2) }}</td>
            </tr>
            


            {{-- <tr>
                <th style="text-align: right;"><b>Total Agreement : </b></th>
                <td style="text-align: right;">{{ round($totalAgreement, 2) }}</td>
            </tr> --}}

        </tfoot>
    </table>


@endsection
