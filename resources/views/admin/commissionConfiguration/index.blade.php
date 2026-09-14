@extends('admin.layouts.master')

@section('content')
    <div style="padding-bottom: 10px;"></div>

    @php
        use App\Product;
        use App\DealerSetup;
        use App\StaffSetup;
    @endphp

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

    <div class="card">            
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                <div class="col-md-6">  
                    <span class="shortlink">
                        <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route($addNewLink)}}">
                            <i class="fa fa-plus-circle"></i> Add New
                        </a>
                    </span>                     
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="10px">SL</th>
                            <th width="250px">Commission Type</th>
                            <th width="300px">Name</th>
                            <th width="30px" style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($allCommissions as $commission)
                            @php
                                $i++;
                                $dealer = DealerSetup::where('id',@$commission->dealer_id)->first();
                                $staff = StaffSetup::where('id',@$commission->staff_id)->first();
                                if(@$dealer)
                                {
                                    $name = $dealer->name;
                                }
                                elseif(@$staff)
                                {
                                    $name = $staff->name;
                                }
                                else
                                {

                                }
                            @endphp
                            <tr class="row_{{$commission->id}}">
                                <td>{{$i}}</td>
                                <td>{{$commission->commission_type}}</td>
                                <td>{{@$name}}</td>
                                <td style="text-align: center;">
                                    @php
                                        echo \App\Link::action($commission->id);
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

                invoiceId = $(this).parent().data('id');
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
                            url : "{{ route('invoiceSetup.delete') }}",
                            data : {invoiceId:invoiceId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Invoice Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+invoiceId).remove();
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