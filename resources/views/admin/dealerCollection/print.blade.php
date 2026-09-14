@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style>
        table {
            font-family: Times, "Times New Roman", serif;
        }

        #report-table td,
        #report-table th {
            border: 1px solid #ddd;
        }

        #report-table tbody td {
            height: 500px;
            vertical-align: top;
        }

        #invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        #invoice-table td {
            width: 50%;
            border: 0px solid black;
        }

        #invoice-header {
            background-color: lightgray;
            width: 100%;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        #invoice-footer {
            background-color: lightgray;
            width: 100%;
        }

        #invoice-footer th {
            border: 0px solid white;
            padding: 5px;
            text-align: right;
            font-size: 14px;
            width: 180px;
        }

        #invoice-footer td {
            border: 1px solid black;
            padding: 5px;
        }

        .moneyReceiptHeader {
            font-size: 13px;
        }

        .bottom {
            border-bottom: 1px solid #000000;
            /*padding: 10px;*/
            vertical-align: text-bottom;
        }

        .moneyReceiptAmountTable {
            border-collapse: collapse;
        }

        .moneyReceiptAmountTable td {
            border: 1px solid #333;
            padding: 5px;
        }

    </style>
@endsection

@section('content')
    <table id="invoice-header">
        <tr>
            <td>Money Receipt</td>
        </tr>
    </table>
    {{-- <caption style="text-decoration: none;"><h2>Money Receipt</h2></caption> --}}
    <div id="pad-bottom"></div>

    <table width="100%" border="0">
        <tbody>
            <tr>
                <td width="100px"><span><b>Payment No</b></span></td>
                <td width="10px">:</td>
                <td>{{ $dealerCollection->payment_no}}</td>

                <td align="right"><span><b>Date</b></span></td>
                <td align="right" width="10px">:</td>
                <td align="right" width="90px">{{ date('d-m-Y', strtotime($dealerCollection->payment_date)) }}</td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table border="0">
        <tr>
            <td width="160px"><strong>Cash Received From</strong> </td>
            <td class="bottom" width="365px">{{ $dealerCollection->dealer->name }}</td>
            <td width="40px"><strong>BDT</strong> </td>
            <td class="bottom" width="190px">{{ round($dealerCollection->payment_amount, 2) }}/-</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table>
        <tr>
            <td width="40px"><strong>For</strong> </td>
            <td class="bottom" width="780px">{{ $dealerCollection->remarks }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="130px"><strong>Collection Mode</strong> </td>
            <td class="bottom" width="780px">{{ $dealerCollection->money_receipt_type}}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="80px"><strong>In Words</strong> </td>
            <td class="bottom" width="750px">
                @php
                    echo $inWords = \App\HelperClass::numberToWords($dealerCollection->payment_amount);
                @endphp
                Taka Only
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>

    @php
    $dueAmount = 0;
    $balance = 0;
    @endphp

    <table width="100%">
        <tr>
            <td width="60%">
                <table class="moneyReceiptAmountTable">
                    {{-- <tr>
                        <td width="200px" align="right">Total Amount</td>
                        <td width="150px" align="right">{{ $dueAmount }}</td>
                    </tr> --}}
                    <tr>
                        <td align="right">Received Amount</td>
                        <td align="right">{{ round($dealerCollection->payment_amount, 2) }}</td>
                    </tr>

                    <tr>
                        <td align="right">Due/Advance Balance</td>
                        <td align="right">{{ round($due, 2) }}</td>
                    </tr>
                </table>
            </td>
            <td width="40%">
                <table width="500px" style="padding-bottom: -80px;">
                    <tr>
                        <td style="border-bottom: 1px solid #333;"></td>
                    </tr>
                    <tr>
                        <td align="center">Received By</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>
@endsection
