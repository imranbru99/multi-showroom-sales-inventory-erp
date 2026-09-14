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
    <div class="col-md-4">
        <label for="showroom">Showroom</label>
        <div class="form-group">
            <select class="form-control chosen-select" name="showroom">
                @foreach ($showrooms as $showroom)
                <option value="{{ $showroom->id }}" @if ($showroomId==$showroom->id) selected @endif>
                    {{ $showroom->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <label for="group">Groups</label>
        <div class="form-group">
            <select class="form-control chosen-select" name="type">
                <option value="1" {{ $type == 1 ? 'selected' : '' }}>Retail</option>
            </select>
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="from-date">From Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y', strtotime(@$fromDate)) }}" readonly>
            </div>
            <div class="col-md-6 form-group">
                <label for="to-date">To Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y', strtotime(@$toDate)) }}" readonly>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <label for="project">Project</label>
        <div class="form-group">
            <select class="form-control chosen-select" name="project[]" multiple>
                <option value=" ">Select Project</option>
                @foreach ($projects as $project)
                <?php
                        if (@$projectId) {
                            if (in_array($project->id, @$projectId)) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        }
                        ?>
                <option value="{{ $project->id }}" {{ @$select }}>{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-8">
        <label for="group">Groups</label>
        <div class="form-group">
            <select class="form-control chosen-select" name="group[]" data-placeholder="Select Group" multiple>
                @foreach ($groupList as $group)
                <?php
                    if (@$groupParam) {
                        if (in_array($group->id, @$groupParam)) {
                            $select = 'selected';
                        } else {
                            $select = '';
                        }
                    }
                    ?>
                <option value="{{ $group->id }}" {{ @$select }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" name="type" value="{{ $type }}">
<input type="hidden" name="showroom" value="{{ $showroomId }}">

<input type="hidden" name="print" value="{{ $print }}">
@if ($projectId)
@foreach ($projectId as $p)
<input type="hidden" name="project[]" value="{{ $p }}">
@endforeach
@endif
@if ($groupParam)
@foreach ($groupParam as $groupInfo)
<input type="hidden" name="group[]" value="{{ $groupInfo }}">
@endforeach
@endif
@endsection

@section('print_card_body')
<form action="{{route('retailTargetAchivement.save')}}" method="post">
    {{csrf_field()}}
    <input type="hidden" name="month_retail" value="{{ date('Y-m', strtotime($fromDate)) }}">
    <input type="hidden" name="showroom" value="{{ $showroomId }}">
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr class="bg-success">
                    <th width="20px">Sl</th>
                    <th>Group</th>
                    <th>Month-Year</th>
                    <th>Employee Name</th>
                    <th>Salary</th>
                    <th>Collect. By Customer</th>
                    <th>Salary Ach</th>
                    <th>Target</th>
                    <th>Cash Ach.</th>
                    <th>Hire Ach.</th>
                    <th>MSP-C Ach.</th>
                    <th>MSP-H Ach.</th>
                    <th>Total Ach.</th>
                    <th>Achive %</th>
                    <th>Cash Comm</th>
                    <th>Total Comm.</th>
                    {{-- <th>Salary Ach</th> --}}
                    <th>Salary Ach</th>
                    <th>Agree.</th>
                    <th>Agree. Comm</th>
                    <th>Net Salary</th>
                </tr>
            </thead>

            <tbody>
                <?php
            $sl = 1;
            ?>
                @foreach ($data as $d)
                <?php
                //customer collection
                if ($d['cus_col_per'] == 100) {
                    $customerAS = $d['leader_info']['salary'];
                } else {
                    if ($d['cus_col_per'] > 0) {
                        $customerAS = $d['leader_info']['salary'] * ($d['cus_col_per'] / 100);
                    } else {
                        $customerAS = 0;
                    }
                }
                
                //target achieve
                if ($d['leader_info']['average'] >= 100) {
                    $over = $d['leader_info']['totalCollection'] - $d['leader_info']['target'];
                    $targetAS = $customerAS + $d['leader_info']['totalCommission'];
                } else {
                    if ($d['leader_info']['average'] > 0) {
                        $targetAS = $customerAS * ($d['leader_info']['average'] / 100);
                    } else {
                        $targetAS = 0;
                    }
                }

                //cash commission
               // $achieve = $targetAS + $d['leader_info']['cash_com'];

                $netSalary = $targetAS;
                if($d['leader_info']['agrCom'] > 0){
                    $netSalary = $d['leader_info']['agrCom'] + $netSalary;
                }
                
                ?>
                <tr class="text-right">
                    <td class="text-center">{{ $sl++ }}</td>
                    <td class="text-center">
                        {{ $d['group'] }}
                        <input type="hidden" name="group_name[]" value="{{ $d['group'] }}">
                    </td>
                    <td class="text-center">
                        {{ $d['month'] }}
                        <input type="hidden" name="month[]" value="{{ $d['month'] }}">
                    </td>
                    <td class="text-left">
                        {{ $d['leader_info']['member'] }}
                        <input type="hidden" name="employee[]" value="{{ $d['leader_info']['member'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['salary'] }}
                        <input type="hidden" name="salary[]" value="{{ $d['leader_info']['salary'] }}">
                    </td>
                    <td>
                        {{ $d['cus_col_per'] }}%
                        <input type="hidden" name="customer[]" value="{{ $d['cus_col_per'] }}">
                    </td>
                    <td>
                        {{ number_format($customerAS, 2, '.', '') }}
                        <input type="hidden" name="c_salary[]" value="{{ number_format($customerAS, 2, '.', '') }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['target'] }}
                        <input type="hidden" name="target[]" value="{{ $d['leader_info']['target'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['cashCollection'] }}
                        <input type="hidden" name="cash[]" value="{{ $d['leader_info']['cashCollection'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['higherCollection'] }}
                        <input type="hidden" name="hire[]" value="{{ $d['leader_info']['higherCollection'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['mspCCollection'] }}
                        <input type="hidden" name="msp_c[]" value="{{ $d['leader_info']['mspCCollection'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['mspHCollection'] }}
                        <input type="hidden" name="msp_h[]" value="{{ $d['leader_info']['mspHCollection'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['totalCollection'] }}
                        <input type="hidden" name="total_ach[]" value="{{ $d['leader_info']['totalCollection'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['average'] }} %
                        <input type="hidden" name="achieve[]" value="{{ $d['leader_info']['average'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['cash_com'] }}
                        <input type="hidden" name="cash_com[]" value="{{ $d['leader_info']['cash_com'] }}">
                    </td>
                    <td>
                        {{ $d['leader_info']['totalCommission'] }}
                        <input type="hidden" name="total_com[]" value="{{ $d['leader_info']['totalCommission'] }}">
                    </td>
                    <td>
                        {{ number_format($targetAS, 2, '.', '') }}
                        <input type="hidden" name="com_salary[]" value="{{ number_format($targetAS, 2, '.', '') }}">
                    </td>
                    {{-- <td>{{ number_format($achieve, 2, '.', '') }}</td> --}}
                    <td>
                        {{ number_format($d['leader_info']['agreement'], 2, '.', '') }}
                        <input type="hidden" name="agreement[]" value="{{ number_format($d['leader_info']['agreement'], 2, '.', '') }}">
                    </td>
                    <td>
                        {{ number_format($d['leader_info']['agrCom'], 2, '.', '') }}
                        <input type="hidden" name="agreement_com[]" value="{{ number_format($d['leader_info']['agrCom'], 2, '.', '') }}">
                    </td>
                    <td>
                        {{ number_format($netSalary, 2, '.', '') }}
                        <input type="hidden" name="net_salary[]" value="{{ $netSalary }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="table table-bordered table-sm mt-5">
        <thead>
            <tr class="bg-success">
                <th width="20px">Sl</th>
                <th width="200px">Month-Year</th>
                <th>MSP Cash Collection</th>
                <th>MSP Hire Collection</th>
                <th>Total Collection</th>
                <th>Total Target</th>
                <th>MSP Cash Commission Without Discount Collection</th>
                <th>MSP Cash Commission Without Discount Commission</th>
                <th>MSP Cash Commission With Discount Collection</th>
                <th>MSP Cash Commission With Discount Commission</th>
                <th>MSP Hire Commission</th>
                <th>Total Commission</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($mspCollection))
            <tr>
                <td>1</td>
                <td>
                    {{ $mspCollection['month'] }}
                    <input type="hidden" name="month_year" value="{{ $mspCollection['month'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['cash'] }}
                    <input type="hidden" name="cash_com_com" value="{{ $mspCollection['cash'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['haire'] }}
                    <input type="hidden" name="hire_com" value="{{ $mspCollection['haire'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['total'] }}
                    <input type="hidden" name="total" value="{{ $mspCollection['total'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['target'] }}
                    <input type="hidden" name="target_com" value="{{ $mspCollection['target'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['CashC'] }}
                    <input type="hidden" name="cash_without_dis_col" value="{{ $mspCollection['CashC'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['CashCC'] }}
                    <input type="hidden" name="cash_without_dis_com" value="{{ $mspCollection['CashCC'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['CashDC'] }}
                    <input type="hidden" name="cash_with_dis_col" value="{{ $mspCollection['CashDC'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['CashDCC'] }}
                    <input type="hidden" name="cash_with_dis_com" value="{{ $mspCollection['CashDCC'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['hireC'] }}
                    <input type="hidden" name="hire_com_com" value="{{ $mspCollection['hireC'] }}">
                </td>
                <td class="text-right">
                    {{ $mspCollection['commission'] }}
                    <input type="hidden" name="total_com_com" value="{{ $mspCollection['commission'] }}">
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <table class="table table-bordered table-sm mt-5">
        <thead>
            <tr class="bg-success">
                <th width="20px">Sl</th>
                <th width="100px">Group</th>
                <th width="200px">Month-Year</th>
                <th>Employee Name</th>
                <th>Commission Amount</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $l = 1;
            ?>
            @foreach ($msp as $m)
            <tr class="text-right">
                <td class="text-center">{{ $l++ }}</td>
                <td class="text-center">
                    {{ $m['group'] }}
                    <input type="hidden" name="group_name_com[]" value="{{ $m['group'] }}">
                </td>
                <td class="text-center">
                    {{ $m['month'] }}
                    <input type="hidden" name="month_year_com[]" value="{{ $m['month'] }}">
                </td>
                <td class="text-left">
                    {{ $m['name'] }}
                    <input type="hidden" name="employee_com[]" value="{{ $m['name'] }}">
                </td>
                <td>
                    {{ number_format($m['amount'], 2, '.', '') }}
                    <input type="hidden" name="commission[]" value="{{ $m['amount'] }}">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button class="btn btn-outline-info btn-lg mt-4 float-right" type="submit">Save Report</button>
</form>
@endsection
