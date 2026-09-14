@extends('admin.layouts.master')

@section('content')
    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="print" value="print">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="dealer">Dealer</label>
                        <div class="form-group">
                            <select class="form-control chosen-select" id="dealer" name="dealer[]" multiple>
                                @foreach ($dealers as $dealerInfo)
                                    @php
                                        $select = "";
                                        if ($dealer)
                                        {
                                            if (in_array($dealerInfo->id, $dealer))
                                            {
                                                $select = "selected";
                                            }
                                            else
                                            {
                                                $select = "";
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $dealerInfo->id }}" {{ $select }}>{{ $dealerInfo->name }}</option>
                                @endforeach
                            </select>
                        </div>  
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="from-date">From Date</label>
                                <input  type="text" class="form-control datepicker" id="from_date" name="fromDate" placeholder="Select Date From">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="to-date">To Date</label>
                                <input  type="text" class="form-control datepicker" id="to_date" name="toDate" placeholder="Select Date To">
                            </div>
                        </div>                                  
                    </div>
                </div>                              
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnSummary" value="Summary"><i class="fa fa-search"></i> Search Summary</button>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnRecord" value="Record"><i class="fa fa-search"></i> Search Details</button>
                    </div>
                </div>              
            </div>
        </div>
    </form>

    @if ($btnSummary != "" || $btnRecord != "")
        <div class="card" style="margin-bottom: 0px;">              
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
                    <div class="col-md-6 text-right">
                        <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($dealer)
                                @foreach ($dealer as $dealerInfo)
                                    <input type="hidden" name="dealer[]" value="{{ $dealerInfo }}">
                                @endforeach
                            @endif

                            <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                            <input type="hidden" name="toDate" value="{{ $toDate }}">
                            <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                            @if ($btnSummary == "Summary")
                                <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintSummary" value="Print Summary"><i class="fa fa-print"></i> Print Commission Summary</button>
                            @endif
                            
                            @if ($btnRecord == "Record")
                                <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintRecord" value="Print Record"><i class="fa fa-print"></i> Print Commission Details</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if ($btnSummary == "Summary")
                    <table id="dataTable" name="commisstionStateTable" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th>Dealer Name</th>
                                <th width="100px">Commission</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 0;
                            @endphp

                            @foreach ($dealerCommissionSummaries as $dealerCommissionSummarie)
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $dealerCommissionSummarie->dealerName }}</td>
                                    <td>{{ number_format($dealerCommissionSummarie->totalCommissionAmount,2, '.', '') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($btnRecord == "Record")
                    <table id="dataTable" name="commissionRecordTable" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th>Dealer Name</th>
                                <th>Category</th>
                                <th width="110px">Sale Amount</th>
                                <th width="130px">Commission Rate</th>
                                <th width="100px">Commission</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 0;
                            @endphp

                            @foreach ($dealerCommissionStatements as $dealerCommissionStatement)
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $dealerCommissionStatement->dealerName }}</td>
                                    <td>{{ $dealerCommissionStatement->categoryName }}</td>
                                    <td class="text-right">{{ number_format($dealerCommissionStatement->saleAmount, 2, '.', '') }}</td>
                                    <td class="text-center">{{ $dealerCommissionStatement->commissionRate }}</td>
                                    <td class="text-right">{{ number_format($dealerCommissionStatement->commissionAmount, 2, '.', '') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif                            
            </div>
        </div>
    @endif
@endsection

{{-- @extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="dealer">Dealer</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="dealer" name="dealer[]" multiple>
                    @foreach ($dealers as $dealerInfo)
                        @php
                            $select = "";
                            if ($dealer)
                            {
                                if (in_array($dealerInfo->id, $dealer))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $dealerInfo->id }}" {{ $select }}>{{ $dealerInfo->name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="from-date">From Date</label>
                    <input  type="text" class="form-control datepicker" id="from_date" name="fromDate" placeholder="Select Date From">
                </div>
                <div class="col-md-6 form-group">
                    <label for="to-date">To Date</label>
                    <input  type="text" class="form-control datepicker" id="to_date" name="toDate" placeholder="Select Date To">
                </div>
            </div>                                  
        </div>
    </div>
@endsection

@section('print_card_header')
    @if ($dealer)
        @foreach ($dealer as $dealerInfo)
            <input type="hidden" name="dealer[]" value="{{ $dealerInfo }}">
        @endforeach
    @endif

    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
				<th>Dealer Name</th>
                <th>Category</th>
				<th width="110px">Sale Amount</th>
                <th width="130px">Commission Rate</th>
                <th width="100px">Commission</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 0;
            @endphp

            @foreach ($dealerCommissionStatements as $dealerCommissionStatement)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $dealerCommissionStatement->dealerName }}</td>
                    <td>{{ $dealerCommissionStatement->categoryName }}</td>
                    <td>{{ $dealerCommissionStatement->saleAmount }}</td>
                    <td>{{ $dealerCommissionStatement->commissionRate }}</td>
                    <td>{{ $dealerCommissionStatement->commissionAmount }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection --}}
