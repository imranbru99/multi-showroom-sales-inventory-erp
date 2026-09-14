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
                     <div class="col-md-10">
                        <label for="reference">Reference</label>
                        <div class="form-group">
                            <select class="form-control chosen-select" id="reference" name="referenceParam[]" data-placeholder="Select Reference" multiple required="">
                                @foreach ($staffList as $staff)
                                    @php
                                        $select = "";
                                        if (@$referenceParam)
                                        {
                                            if (in_array($staff->id, $referenceParam))
                                            {
                                                $select = "selected";
                                            }
                                            else
                                            {
                                                $select = "";
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $staff->id }}" {{ $select }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>  
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
                        @if (@$referenceParam)
                            @foreach ($referenceParam as $reference)
                                <input type="hidden" name="reference[]" value="{{ $reference }}">
                            @endforeach
                        @endif
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-print"></i> Print</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped"  name="showroomTable">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th width="20px">System Id</th>
                            <th width="120px">Customer Code</th>
                            <th>Customer Name</th>
                            <th width="120px">National ID</th>
                            <th width="120px">Phone No</th>
                            <th>Address</th>
                            <th>Reference</th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($customers as $customer)
                        @php
                            $i++;
                        @endphp
                            <tr class="row_{{ $customer->id }}">
                                <td>{{$i}}</td>
                                <td>{{$customer->id}}</td>
                                <td>{{$customer->code}}</td>
                                <td>{{$customer->name}}</td>
                                <td>{{$customer->nid}}</td>
                                <td>{{$customer->phone_no}}</td>
                                <td>{{$customer->present_address}}</td>
                                <td>{{@$customer->referenceName}}</td>
                                <td>
                                    @php
                                        echo \App\Link::action($customer->id);
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

                customerId = $(this).parent().data('id');
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
                            url : "{{ route('customerRegistraionSetup.delete') }}",
                            data : {customerId:customerId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Showroom Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+customerId).remove();
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
                            text: "Your Showrrom is safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            }); 

        });
                
    </script>
@endsection