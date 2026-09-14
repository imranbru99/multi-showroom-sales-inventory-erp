@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="print" value="print">
        </div>
    </div>


    <div class="row">
        <div class="col-md-3 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) ?? '' }}" placeholder="Select Date From">
        </div>
        <div class="col-md-3 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDate)) ?? '' }}" placeholder="Select Date To">
        </div>

        <div class="col-md-6">
            <label for="showroom">Showroom</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="showroom[]" multiple>
                    @foreach ($showrooms as $showRoom)
                        <?php
                        if (in_array($showRoom->id, $showroom)) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <option value="{{ $showRoom->id }}" {{ $selected }}>{{ $showRoom->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">

    <input type="hidden" id="type" name="showroom" value="{{ implode(',', $showroom) }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection



@section('print_card_body')
    @if (!empty($print))
        <div class="card-body">
            <div class="table-responsive mb-5">
                <?php
                
                $netPrevious = 0;
                $netSales = 0;
                $netReturns = 0;
                $netActualSales = 0;
                $netCollections = 0;
                $netOutstanding = 0;
                $netAgree = 0;
                ?>

                @foreach ($retails as $retail)
                    <div class="bg-primary text-center text-white p-2 rounded mb-4 d-flex justify-content-between">
                        <h3 class="mb-0">Showroom - {{ $retail['showroom'] }}</h3>
                        <h3 class="mb-0">Type- Retail</h3>
                    </div>
                    <table class="table table-bordered table-striped mb-5">
                        <thead>
                            <tr class="text-center bg-success text-white">
                                <th>Project Name</th>
                                <th>Previous Outstanding</th>
                                <th>Sales</th>
                                <th>Returns</th>
                                <th>Actual Sales</th>
                                <th>Collections</th>
                                <th>Current Outstanding</th>
                                <th>Agreement Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalPrevious = 0;
                            $totalSales = 0;
                            $totalReturns = 0;
                            $totalActualSales = 0;
                            $totalCollections = 0;
                            $totalOutstanding = 0;
                            $totalAgree = 0;
                            ?>
                            @foreach ($retail['info'] as $info)
                                <?php
                                $totalPrevious += $info['previous'];
                                $totalSales += $info['sales'];
                                $totalReturns += $info['returns'];
                                $totalActualSales += $info['actualSales'];
                                $totalCollections += $info['collections'];
                                $totalOutstanding += $info['previous'] + $info['outstanding'];
                                $totalAgree += $info['agreement'];
                                ?>
                                <tr class="text-right">
                                    <td class="text-left" width="30%">{{ $info['project'] }}</td>
                                    <td width="10%">
                                        @if ($info['previous'] > 0)
                                            {{ $info['previous'] }}
                                        @else
                                            <span
                                                class="text-danger">({{ number_format(abs($info['previous']), 2, '.', '') }})</span>
                                        @endif
                                    </td>
                                    <td width="12%">{{ $info['sales'] }}</td>
                                    <td width="12%">{{ $info['returns'] }}</td>
                                    <td width="12%">{{ $info['actualSales'] }}</td>
                                    <td width="12%">{{ $info['collections'] }}</td>
                                    <td width="12%">
                                        @if ($info['previous'] + $info['outstanding'] > 0)
                                            {{ $info['previous'] + $info['outstanding'] }}
                                        @else
                                            <span
                                                class="text-danger">({{ number_format(abs($info['previous'] + $info['outstanding']), 2, '.', '') }})</span>
                                        @endif
                                    </td>
                                    <td width="12%">{{ $info['agreement'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-right bg-success">
                                <td class="text-white" width="30%"><b>Total</b></td>
                                <td class="text-white" width="12%">
                                    <b>{{ number_format($totalPrevious, 2, '.', '') }}</b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>{{ number_format($totalSales, 2, '.', '') }}</b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>{{ number_format($totalReturns, 2, '.', '') }}</b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>
                                        @if ($totalActualSales > 0)
                                            {{ number_format($totalActualSales, 2, '.', '') }}
                                        @else
                                            ({{ number_format(abs($totalActualSales), 2, '.', '') }})
                                        @endif
                                    </b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>{{ number_format($totalCollections, 2, '.', '') }}</b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>
                                        @if ($totalOutstanding > 0)
                                            {{ number_format($totalOutstanding, 2, '.', '') }}
                                        @else
                                            ({{ number_format(abs($totalOutstanding), 2, '.', '') }})
                                        @endif
                                    </b>
                                </td>
                                <td class="text-white" width="12%">
                                    <b>{{ number_format($totalAgree, 2, '.', '') }}</b>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php
                    $netPrevious += $totalPrevious;
                    $netSales += $totalSales;
                    $netReturns += $totalReturns;
                    $netActualSales += $totalActualSales;
                    $netCollections += $totalCollections;
                    $netOutstanding += $totalOutstanding;
                    $netAgree += $totalAgree;
                    ?>
                @endforeach
            </div>


            <div class="table-responsive my-5">
                <h3 class="bg-primary text-center text-white p-2 rounded mb-2">Dealer</h3>
                <table class="table table-bordered table-striped mb-5">
                    <thead>
                        <tr class="text-center bg-success text-white">
                            <th>Previous Outstanding</th>
                            <th>Sales</th>
                            <th>Returns</th>
                            <th>Actual Sales</th>
                            <th>Collections</th>
                            <th>Current Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-right">
                            <td>
                                @if ($dealerDetails['previous'] > 0)
                                    {{ $dealerDetails['previous'] }}
                                @else
                                    <span
                                        class="text-danger">({{ number_format(abs($dealerDetails['previous']), 2, '.', '') }})</span>
                                @endif
                            </td>
                            <td>{{ $dealerDetails['sales'] }}</td>
                            <td>{{ $dealerDetails['returns'] }}</td>
                            <td>{{ $dealerDetails['actualSales'] }}</td>
                            <td>{{ $dealerDetails['collections'] }}</td>
                            <td>
                                @if ($dealerDetails['outstanding'] + $dealerDetails['previous'] > 0)
                                    {{ $dealerDetails['outstanding'] + $dealerDetails['previous'] }}
                                @else
                                    <span
                                        class="text-danger">({{ number_format(abs($dealerDetails['outstanding'] + $dealerDetails['previous']), 2, '.', '') }})</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table class="table table-bordered table-striped mb-5">
                <thead>
                    <tr class="text-right bg-dark text-white">
                        <th width="18%">Net Total</th>
                        <th width="12%">
                            @if ($netPrevious + $dealerDetails['previous'] > 0)
                                {{ number_format($netPrevious + $dealerDetails['previous'], 2, '.', '') }}
                            @else
                                ({{ number_format(abs($netPrevious + $dealerDetails['previous']), 2, '.', '') }})
                            @endif
                        </th>
                        <th width="12%">{{ number_format($netSales + $dealerDetails['sales'], 2, '.', '') }}</th>
                        <th width="12%">{{ number_format($netReturns + $dealerDetails['returns'], 2, '.', '') }}</th>
                        <th width="12%">
                            @if ($netActualSales + $dealerDetails['actualSales'] > 0)
                                {{ number_format($netActualSales + $dealerDetails['actualSales'], 2, '.', '') }}
                            @else
                                ({{ number_format(abs($netActualSales + $dealerDetails['actualSales']), 2, '.', '') }})
                            @endif
                        </th>
                        <th width="12%">{{ number_format($netCollections + $dealerDetails['collections'], 2, '.', '') }}
                        </th>
                        <th width="12%">
                            @if ($netOutstanding + $dealerDetails['outstanding'] + $dealerDetails['previous'] > 0)
                                {{ number_format($netOutstanding + $dealerDetails['outstanding'] + $dealerDetails['previous'],2,'.','') }}
                            @else
                                ({{ number_format(abs($netOutstanding + $dealerDetails['outstanding'] + $dealerDetails['previous']),2,'.','') }})
                            @endif
                        </th>
                        <th width="12%">{{ number_format($netAgree, 2, '.', '') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endif
@endsection
