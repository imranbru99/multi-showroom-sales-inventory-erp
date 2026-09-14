@extends('admin.layouts.master')

@section('content')

    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <h1 class="text-center h3">{{ $title }}</h1>
        </div>

        <form action="{{ route('customer.agreements.report1') }}" method="get" id="searchForm">

            {{ csrf_field() }}

            <input type="hidden" name="searched" value="true">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="text" name="start_date" class="form-control datepicker"
                                value="{{ date('d-m-Y', strtotime($startDate)) }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="text" name="end_date" class="form-control datepicker"
                                value="{{ date('d-m-Y', strtotime($endDate)) }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="collector">Collector</label>
                            <select name="collector" id="collector" class="form-control chosen-select">
                                <option value="">Select a collector</option>
                                @foreach ($collectors as $collector)
                                    <option value="{{ $collector->id }}" @if ($collector->id == $collector_id) selected @endif>
                                        {{ $collector->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-outline-info btn-lg" type="submit" name="Fsubmit"
                            value="searched">Search</button>
                        <button class="btn btn-outline-info btn-lg" type="button" name="Fsubmit" value="printed"
                            id="PrintPage">Print</button>
                    </div>
                </div>

            </div>

        </form>

    </div>

    <div class="row mt-2">

        <div class="col-md-12">
            <div class="tableFixHead">
                <table id="dataTable" class="table table-bordered table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th style="width:50px;">SL#</th>
                            <th style="width:105px;">Date</th>
                            <th style="width:105px;">A/C No.</th>
                            <th style="width:120px;">Customer Name</th>
                            <th style="width:105px;">Reference</th>
                            <th style="width:105px;">Memo No/ MR. No</th>
                            <th style="width:92px;">Agreement Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agreements as $agreement)
                            <tr>
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($agreement->date)) }}</td>
                                <td>{{ @$agreement->customer->code }}</td>
                                <td>{{ @$agreement->customer->name }}</td>
                                <td>{{ @$agreement->staff->name }}</td>
                                <td>{{ @$agreement->money_r_no }}</td>
                                <td>{{ @$agreement->agreement_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection


@section('custom-js')
    <script>
        $(function() {

            $('#PrintPage').click(function(e) {

                e.preventDefault();

                let form = $('#searchForm');

                form.attr('target', '_blank');

                console.log(form);

                form.submit();

            });

        });
    </script>
@endsection
