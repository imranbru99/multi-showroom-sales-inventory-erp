@extends('admin.layouts.master')

@section('content')
    <div style="padding-bottom: 10px;"></div>

    @php
        $message = Session::get('msg');
        $erroMesege = Session::get('err_msg')
    @endphp

    @if (isset($message))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> {{ $message }}
        </div>
    @endif

    @if (isset($erroMesege))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Oops! </strong> {{ $erroMesege }}
        </div>
    @endif

    @php
        Session::forget('msg');
    @endphp

    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="get">
        {{ csrf_field() }}
        <div class="card">           
            <div class="card-header">
                <input type="hidden" name="print" value="print">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 form-group">
                        <label for="from-date">From Date</label>
                        <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
                    </div>
                    <div class="col-md-5 form-group">
                        <label for="to-date">To Date</label>
                        <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
                    </div>

                    <div class="col-md-2">
                        <label for=""></label>
                        <div class="form-group">
                            <button type="submit" id="search" name="searchButton" class="btn btn-outline-info btn-md waves-effect" style="width: 100%;"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-12"><h4 class="card-title">Searched Report</h4></div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped" name="debitTable">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th width="150px">Company Name</th>
                            <th width="120px">Vouchar No</th>
                            <th width="60px">Vouchar Type</th>
                            <th width="60px">Date</th>
                            <th width="50px">Amount</th>
                            <th width="90px">Approve Status</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 1;
                        @endphp
                        @foreach ($voucherLists as $voucherList)
                            <tr class="row_{{ $voucherList->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ @$voucherList->showroom->name }}</td>
                                <td>{{ @$voucherList->voucher_no }}</td>
                                <td>{{ $voucherList->voucher_type }}</td>
                                <td>{{ date('d-m-Y',strtotime($voucherList->voucher_date)) }}</td>
                                <td align="right">
                                    @if ($voucherList->debit_amount > 0)
                                    {{ $voucherList->debit_amount }}
                                    @else
                                    {{ $voucherList->credit_amount }}
                                    @endif
                                </td>
                                <td align="center">
                                    @php
                                        if ($voucherList->approve == 1)
                                        {
                                            echo "Approve";
                                        }
                                        else
                                        {
                                            echo "Pending";
                                        }
                                    @endphp
                                </td>
                                <td align="center">
                                    <span>
                                        <a class="btn btn-outline-success btn-sm" href="{{ route('voucherRefuse.view',$voucherList->id) }}"><i class="fa fa-eye"></i> View</a>                                        
                                    </span>
                                    <span class="btn btn-outline-danger btn-sm remove-item" onclick="approveStatus({{ $voucherList->id }})">
                                        <i class="{{ $voucherList->approve == 0 ? 'fa fa-thumbs-up' : 'fa fa-thumbs-down' }}"></i> {{ $voucherList->approve == 0 ? 'Apporve' : 'Refuse' }}
                                    </span>
                                </td>
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
        //ajax status change code
        function approveStatus(voucherApproveId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('voucherRefuse.approve') }}",
                data: {voucherApproveId:voucherApproveId},
                success: function(response) {
                    setTimeout(function(){  // wait for 1 secs(2)
                        location.reload(); // then reload the page.(3)
                    }, 1000)
                    swal({
                        title: "<small class='text-success'>Success!</small>", 
                        type: "success",
                        text: "Successfully Approved!",
                        timer: 1000,
                        html: true,
                    });
                },
                error: function(response) {
                    error = "Failed.";
                    swal({
                        title: "<small class='text-danger'>Error!</small>", 
                        type: "error",
                        text: error,
                        timer: 2000,
                        html: true,
                    });
                }
            });
        }                
    </script>
@endsection