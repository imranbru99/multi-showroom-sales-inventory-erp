@extends('admin.layouts.master')

@section('content')

    <div style="padding-bottom: 10px;"></div>

    @php
        $message = Session::get('msg');
    @endphp

    @if (isset($message))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> {{ $message }}
        </div>
    @endif

    @php
        Session::forget('msg');
    @endphp

    <div class="card">            
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                <div class="col-md-6 text-right">
                    <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route($addNewLink) }}">
                        <i class="fa fa-plus-circle"></i> Add New
                    </a>                  
                </div>
            </div>
        </div>
    
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Collection</th>
                            <th width="20px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($collections as $collection)
                            <tr class="row_{{ $collection->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d-m-Y',strtotime($collection->collection_date)) }}</td>
                                <td>{{ @$collection->staff->name }} ({{ @$collection->staff->code }})</td>
                                <td>{{ number_format($collection->amount, 2, '.', '') }}</td>
                                <td>
                                    @php
                                        echo \App\Link::action($collection->id);
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

                id = $(this).parent().data('id');
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
                            url : "{{ route('staffLoanCollection.delete') }}",
                            data : {id:id},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Collection Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+id).remove();
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
                            text: "Your Loan Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
          
    </script>
@endsection