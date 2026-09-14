@extends('admin.layouts.masterPrint')

@section('content')
    @php
    use App\InstallmentCollection;
    use App\CustomerRegistrationSetup;
    use App\Product;
    @endphp
    <table id="report-table">
        <caption>
            {{ @$title }}
            @if ($employeeId)
                <?php
                $staff = \App\StaffSetup::find($employeeId);
                ?>
                - OF {{ $staff->name }}
            @endif
        </caption>
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>A/C No</th>
                <th>Client Name</th>
                <th>Phone No</th>
                <th>Address</th>
                <th>Refference By</th>
                <th>Purchase Commission</th>
                <th>Cash Price</th>
                <th>Sales Price</th>
                <th>Profit/Loss (%)</th>
                @if ($type == 1)
                    <th>With Discount /Commission (%)</th>
                    <th>With Discount /Commission</th>
                @else
                    <th>Without Discount /Commission (%)</th>
                    <th>Without Discount /Commission</th>
                @endif
                <th>Employee Commission (2%)</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $totalCashPrice = 0;
            $totalSalesPrice = 0;
            $totalPL = 0;
            $totalCP = 0;
            $totalCA = 0;
            $totalEC = 0;
            ?>
            @foreach ($cashData as $cash)
                <?php
                $totalCashPrice += $cash['cash_price'];
                $totalSalesPrice += $cash['sales_price'];
                $totalPL += $cash['profit_loss'];
                $totalCP += $cash['commission_percent'];
                $totalCA += $cash['commission_amount'];
                $totalEC += $cash['employee_commission'];
                ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cash['invoice_no'] }}</td>
                    <td>{{ $cash['date'] }}</td>
                    <td>{{ $cash['ac_no'] }}</td>
                    <td>{{ $cash['client_name'] }}</td>
                    <td>{{ $cash['phone_no'] }}</td>
                    <td>{{ $cash['address'] }}</td>
                    <td>{{ $cash['reference_by'] }}</td>
                    <td align="right">{{ $cash['purchase_com'] }}</td>
                    <td align="right">{{ $cash['cash_price'] }}</td>
                    <td align="right">{{ $cash['sales_price'] }}</td>
                    <td align="right">{{ $cash['profit_loss'] }}%</td>
                    <td align="right">{{ $cash['commission_percent'] }}%</td>
                    <td align="right">{{ $cash['commission_amount'] }}</td>
                    <td align="right">{{ $cash['employee_commission'] }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <?php
            $count = !empty($cashData) ? count($cashData) : 0;
            if ($count > 0) {
                $totalPL = $totalPL / $count;
                $totalCP = $totalCP / $count;
            }
            ?>
            <tr>
                <th colspan="9">Total</th>
                <th>{{ number_format($totalCashPrice, 2, '.', '') }}</th>
                <th>{{ number_format($totalSalesPrice, 2, '.', '') }}</th>
                <th>{{ number_format($totalPL, 2, '.', '') }}%</th>
                <th>{{ number_format($totalCP, 2, '.', '') }}%</th>
                <th>{{ number_format($totalCA, 2, '.', '') }}</th>
                <th>{{ number_format($totalEC, 2, '.', '') }}</th>
            </tr>
        </tfoot>
    </table>
@endsection
