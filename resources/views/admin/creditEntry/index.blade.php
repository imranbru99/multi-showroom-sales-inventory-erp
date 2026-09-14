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

    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="card">           
            <div class="card-header">
                <input type="hidden" name="print" value="print">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route($addNewLink)}}">
                            <i class="fa fa-plus-circle"></i> Add New
                        </a>                    
                    </div>
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
                <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
                <div class="col-md-6 text-right">
                    <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                        <input type="hidden" name="toDate" value="{{ $toDate }}">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-print"></i> Print</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped" name="debitTable">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th width="100px">Vouchar No</th>
                            <th width="60px">Date</th>
                            <th width="150px">Debit Head</th>
                            <th width="150px">Credit Head</th>
                            <th width="50px">Amount</th>
                            <th width="90px">Account Status</th>
                            <th width="40px">Publish</th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 1;
                        @endphp
                        @foreach ($transactionLists as $transactionList)
                            <tr class="row_{{ $transactionList->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ $transactionList->voucher_no }}</td>
                                <td>{{ date('d-m-Y',strtotime($transactionList->voucher_date)) }}</td>
                                <td>{{ $transactionList->debitHeadname }}</td>
                                <td>{{ $transactionList->creditHeadName }}</td>
                                <td align="right">{{ $transactionList->debit_amount }}</td>
                                <td align="center">
                                    @php
                                        if ($transactionList->approve == 1)
                                        {
                                            echo "Approve";
                                        }
                                        else
                                        {
                                            echo "Pending";
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo \App\Link::status($transactionList->id,$transactionList->active);
                                    @endphp                             
                                </td>
                                <td>
                                    @php
                                        echo \App\Link::action($transactionList->id);
                                    @endphp                             
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
        $(document).ready(function() {
            var updateThis ;         

            //ajax delete code
            $('#dataTable tbody').on( 'click', 'i.fa-trash', function () {
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                creditEntryId = $(this).parent().data('id');
                var tableRow = this;
                swal({   
                    title: "Are you sure?",   
                    text: "You will not be able to recover this information!",   
                    type: "warning",   
                    showCancelButton: true,   
                    confirmButtonColor: "#DD6B55",   
                    confirmButtonText: "Yes, delete it!",   
                    cancelButtonText: "No, cancel plx!",   
                    closeOnConfirm: false,   
                    closeOnCancel: false 
                },
                function(isConfirm){   
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            url : "{{ route('creditEntry.delete') }}",
                            data : {creditEntryId:creditEntryId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Credit Voucher Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+creditEntryId).remove();
                            },
                            error: function(response) {
                                error = "Failed.";
                                swal({
                                    title: "<small class='text-danger'>Error!</small>", 
                                    type: "error",
                                    text: error,
                                    timer: 1000,
                                    html: true,
                                });
                            }
                        });    
                    }
                    else
                    { 
                        swal({
                            title: "Cancelled", 
                            type: "error",
                            text: "Your Credit Voucher is safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            }); 

        });
                
        //ajax status change code
        function statusChange(creditEntryId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('creditEntry.publish') }}",
                data: {creditEntryId:creditEntryId},
                success: function(response) {
                    swal({
                        title: "<small class='text-success'>Success!</small>", 
                        type: "success",
                        text: "Successfully Published!",
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