@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="dealerTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th width="100px">Name</th>
                        <th width="100px">Type</th>
                        <th width="150px">Dates</th>
                      <!--   <th width="150px">Weeks</th> -->
                      
                       <!--  <th width="20px">Status</th> -->
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach ($holidays as $holiday)
                        <tr class="row_{{ $holiday->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ $holiday->name }}</td>
                            <td>{{ $holiday->type }}</td>
                            <td>
                                @if ($holiday->from_date == "")
                                    {{ $holiday->to_date }}
                                @else
                                    {{ $holiday->from_date }} - {{ $holiday->to_date }}
                                @endif
                            </td>
                       <!--      <td>
                                @if ($holiday->from_date == "")
                                    {{ date('l',strtotime($holiday->to_date)) }}
                                @else
                                    {{ date('l',strtotime($holiday->from_date)) }} - {{ date('l',strtotime($holiday->to_date)) }}
                                @endif
                            </td> -->
                          
                           <!--  <td>
                                <?php echo \App\Link::status($holiday->id,$holiday->status)?>
                            </td> -->
                            <td>
                                @php
                                    echo \App\Link::action($holiday->id);
                                @endphp                             
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

                holidayId = $(this).parent().data('id');
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
                            url : "{{ route('holidaysSetup.delete') }}",
                            data : {holidayId:holidayId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Dealer Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+holidayId).remove();
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
                            text: "Your Dealer Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
                
        //ajax status change code
        function statusChange(holidayId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('holidaysSetup.status') }}",
                data: {holidayId:holidayId},
                success: function(response) {
                    swal({
                        title: "<small class='text-success'>Success!</small>", 
                        type: "success",
                        text: "Status Successfully Updated!",
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