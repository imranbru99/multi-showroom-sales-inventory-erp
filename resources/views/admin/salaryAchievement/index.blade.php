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
        <div class="col-md-6">
            <label for="group">Groups</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="group[]" data-placeholder="Select Group" multiple>
                    <option value=" ">Select Groups</option>
                    @foreach ($groupList as $group)
                        @php
                            if (@$groupParam) {
                                if (in_array($group->id, @$groupParam)) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            }
                        @endphp
                        <option value="{{ $group->id }}" {{ @$select }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="from-date">Month</label>
                    <div class="form-group">
                        @php
                            $select = '';
                            $months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            $month = date('m');
                        @endphp
                        <select class="form-control chosen-select" id="month" name="month">
                            @foreach ($months as $key => $value)
                                @php
                                    if ($reqMonth) {
                                        if ($key == $reqMonth) {
                                            $select = 'selected';
                                        } else {
                                            $select = '';
                                        }
                                    } else {
                                        if ($key == $month) {
                                            $select = 'selected';
                                        } else {
                                            $select = '';
                                        }
                                    }
                                @endphp
                                <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="from_date">Year {{ $reqYear }}</label>
                    <select class="form-control chosen-select" id="year" name="year">
                        <option value="">Select Year</option>
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for ($i = $currentYear; $i >= 1900; $i--)
                            @php
                                if ($reqYear) {
                                    if ($i == $reqYear) {
                                        $select = 'selected';
                                    } else {
                                        $select = '';
                                    }
                                } else {
                                    if ($i == $currentYear) {
                                        $select = 'selected';
                                    } else {
                                        $select = '';
                                    }
                                }
                            @endphp
                            <option value="{{ $i }}" {{ $select }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    @if ($groupParam)
        @foreach ($groupParam as $groupInfo)
            <input type="hidden" name="group[]" value="{{ $groupInfo }}">
        @endforeach
    @endif
@endsection

@section('print_card_body')
    <table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Group</th>
                <th width="200px">Members</th>
                <th width="150px">Month-Year</th>
                <th width="150px" style="text-align: center;">Target</th>
                <th width="150px" style="text-align: center;">Achivement</th>
                <th width="150px" style="text-align: center;">Difference</th>
                <th width="50px" style="text-align: center;">Achive % </th>
                <th width="50px" style="text-align: center;">Salary Eligible</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 0;
            @endphp
            @foreach ($targets as $target)
                @php
                    if ($target['collection'] < $target['target']) {
                        $changeColor = 'red';
                    } elseif ($target['collection'] == $target['target']) {
                        $changeColor = 'green';
                    } else {
                        $changeColor = '';
                    }
                    $comma = '';
                    if (!empty($target['members']) && count($target['members']) > 1) {
                        $comma = ',';
                    }
                    $monthcomma = '';
                    if (!empty($target['month']) && count($target['month']) > 1) {
                        $monthcomma = ',';
                    }
                    
                @endphp
                <tr class="{{ @$changeColor }}">
                    <td>{{ $sl++ }}</td>
                    <td>{{ $target['group'] }}</td>
                    <td>
                        @foreach ($target['members'] as $key => $value)
                            {{ $value }} {{ $comma }}
                        @endforeach
                    </td>
                    <td>
                        @foreach ($target['month'] as $key => $value)
                            {{ $value }} {{ $monthcomma }}
                        @endforeach
                    </td>
                    <td style="text-align: center;">{{ $target['target'] }}</td>
                    <td style="text-align: center;">{{ $target['collection'] }}</td>
                    <td style="text-align: center;">{{ $target['difference'] }}</td>
                    <td style="text-align: center;">{{ $target['average'] }}%</td>
                    <td style="text-align: center;">{{ $target['salaryAchieve'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
