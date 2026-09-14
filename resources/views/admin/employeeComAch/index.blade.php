@extends('admin.layouts.masterReport')

@section('search_card_body')
    @php
    use App\InstallmentCollection;
    @endphp
    <style type="text/css">
        .red {
            color: red;
        }

        .green {
            color: green;
        }
  
    </style>
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="from-date">Showroom</label>
            <select class="form-control chosen-select" name="showroom_id" data-placeholder="Select Showroom">
                @foreach ($showrooms as $showroom)
                    <option value="{{ $showroom->id }}" @if ($showroom->id == $showroomId) selected @endif>
                        {{ $showroom->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label for="month">Month</label>
            <input type="month" class="form-control" name="month" placeholder="Select Date From"
                value="{{ !empty($month) ? @$month : date('Y-m') }}">
        </div>
        <div class="col-md-12">
            <label for="group">Employee</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="employee[]" data-placeholder="Select Employee" multiple>
                    @foreach ($staffs as $staff)
                        <?php
                        if (@$employee) {
                            if (in_array($staff->id, @$employee)) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        }
                        ?>
                        <option value="{{ $staff->id }}" {{ @$select }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="print" value="{{ $print }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="showroom_id" value="{{ $showroomId }}">

    @if ($employee)
        @foreach ($employee as $e)
            <input type="hidden" name="staff[]" value="{{ $e }}">
        @endforeach
    @endif
@endsection

@section('print_card_body')
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr class="bg-success">
                    <th width="20px">Sl</th>
                    <th>EmployeeName</th>
                    {{-- <th>DealerCollection Target</th> --}}

                    @if ($showroomId == 1)
                        <th>Dealer Target</th>
                        <th>DealerCollection Commission</th>
                    @endif


                    {{-- <th>Retail CashCollection Target</th> --}}
                    <th>Retail Target</th>
                    <th>Retail Discount Collection Commission</th>
                    <th>Retail Cash Without Discount Collection Commission</th>
                    <th>Retail Cash With Discount Collection Commission</th>
                    {{-- <th>Retail HireCollection Target</th> --}}
                    <th>Retail HireCollection Commission</th>
                    {{-- <th>Recovery CashCollection Target</th> --}}


                    @if ($showroomId == 1)
                        <th>Recovery CashCollection Commission</th>
                        {{-- <th>Recovery HireCollection Target</th> --}}
                        <th>Recovery HireCollection Commission</th>
                        {{-- <th>MSPCHTarget</th> --}}
                        <th>MSP Cash Discount Commission</th>
                        <th>MSP Cash Without Discount Commission</th>
                        <th>MSP Cash With Discount Commission</th>
                        <th>MSP Hire Commission</th>
                    @endif
                    <th>Total Commission</th>
                </tr>
            </thead>

            <tbody>
                @if ($data)
                    <?php
                    if ($showroomId == 1) {
                        $totalCollection = $data['collections']['dealer'] + $data['collections']['retailCc'] + $data['collections']['retailC'] + $data['collections']['retailDC'] + $data['collections']['retailH'] + $data['collections']['mspCc'] + $data['collections']['mspC'] + $data['collections']['mspDC'] + $data['collections']['mspH'];
                        $totalCommssion = $data['commissions']['dealer'] + $data['commissions']['retailCc'] + $data['commissions']['retailC'] + $data['commissions']['retailDC'] + $data['commissions']['retailH'] + $data['commissions']['mspCc'] + $data['commissions']['mspC'] + $data['commissions']['mspDC'] + $data['commissions']['mspH'];
                    } else {
                        $totalCollection = $data['collections']['retailCc'] + $data['collections']['retailC'] + $data['collections']['retailDC'] + $data['collections']['retailH'];
                        $totalCommssion = $data['commissions']['retailCc'] + $data['commissions']['retailC'] + $data['commissions']['retailDC'] + $data['commissions']['retailH'];
                    }
                    
                    ?>
                    <tr class="text-right">
                        <td class="text-left">1</td>
                        <td class="text-left">Total Collection</td>

                        @if ($showroomId == 1)
                            <td>{{ $data['collections']['dealerTarget'] }}</td>
                            <td>{{ $data['collections']['dealer'] }}</td>
                        @endif


                        <td>{{ $data['collections']['retailTarget'] }}</td>
                        <td>{{ $data['collections']['retailCc'] }}</td>
                        <td>{{ $data['collections']['retailC'] }}</td>
                        <td>{{ $data['collections']['retailDC'] }}</td>
                        <td>{{ $data['collections']['retailH'] }}</td>


                        @if ($showroomId == 1)
                            <td>{{ $data['collections']['reC'] }}</td>
                            <td>{{ $data['collections']['reH'] }}</td>
                            <td>{{ $data['collections']['mspCc'] }}</td>
                            <td>{{ $data['collections']['mspC'] }}</td>
                            <td>{{ $data['collections']['mspDC'] }}</td>
                            <td>{{ $data['collections']['mspH'] }}</td>
                        @endif
                        <td>{{ number_format($totalCollection, 2, '.', '') }}</td>
                    </tr>
                    <tr class="text-right">
                        <td class="text-left">2</td>
                        <td class="text-left text-nowrap">Total Commission</td>


                        @if ($showroomId == 1)
                            <td></td>
                            <td>{{ $data['commissions']['dealer'] }}</td>
                        @endif



                        <td></td>
                        <td>{{ $data['commissions']['retailCc'] }}</td>
                        <td>{{ $data['commissions']['retailC'] }}</td>
                        <td>{{ $data['commissions']['retailDC'] }}</td>
                        <td>{{ $data['commissions']['retailH'] }}</td>



                        @if ($showroomId == 1)
                            <td>{{ $data['commissions']['reC'] }}</td>
                            <td>{{ $data['commissions']['reH'] }}</td>
                            <td>{{ $data['commissions']['mspCc'] }}</td>
                            <td>{{ $data['commissions']['mspC'] }}</td>
                            <td>{{ $data['commissions']['mspDC'] }}</td>
                            <td>{{ $data['commissions']['mspH'] }}</td>
                        @endif


                        <td>{{ number_format($totalCommssion, 2, '.', '') }}</td>
                    </tr>
                    @foreach ($data['data'] as $d)
                        <?php
                        $memCommission = $d['dealer'] + $d['retailCc'] + $d['retailC'] + $d['retailDC'] + $d['reC'] + $d['reH'] + $d['retailH'] + $d['mspCc'] + $d['mspC'] + $d['mspDC'] + $d['mspH'];
                        $employeeTarget = \App\EmployeeCommissionAllocationList::where('staff_id', $d['dealer_id'])
                            ->where('showroom_id', $showroomId)
                            ->with(['allocation'])
                            ->whereHas('allocation', function($q) use($month){
                                $q->where('month', $month);
                            })
                            ->get();
                        ?>
                        <tr class="text-right">
                            <td class="text-left">{{ $loop->iteration + 2 }}</td>
                            <td class="text-left">{{ $d['name'] }}</td>


                            @if ($showroomId == 1)
                                <td></td>
                                <td>{{ $d['dealer'] }} <br> ({{ $employeeTarget->sum('dealer_collection') }}%)</td>
                            @endif


                            <td></td>
                            <td>{{ $d['retailCc'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)</td>
                            <td>{{ $d['retailC'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)</td>
                            <td>{{ $d['retailDC'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)</td>
                            <td>{{ $d['retailH'] }} <br> ({{ $employeeTarget->sum('retail_hire_collection') }}%)</td>



                            @if ($showroomId == 1)
                                <td>{{ $d['reC'] }} <br> ({{ $employeeTarget->sum('recovery_cash_collection') }}%)</td>
                                <td>{{ $d['reH'] }} <br> ({{ $employeeTarget->sum('recovery_hire_collection') }}%)</td>
                                <td>{{ $d['mspCc'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
                                <td>{{ $d['mspC'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
                                <td>{{ $d['mspDC'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
                                <td>{{ $d['mspH'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
                            @endif

                            <td>{{ number_format($memCommission, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection
