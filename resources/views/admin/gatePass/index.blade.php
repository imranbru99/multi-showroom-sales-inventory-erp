@extends('admin.layouts.masterIndex')

@section('custom_css')
    <style type="text/css">
        .table th {
            background: #00c292;
            text-align: center;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th width="150px">Date</th>
                        <th width="150px">Gate Pass No.</th>
                        <th>Dealer</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gatePasses as $gatePasse)
                        <tr id="{{ $gatePasse->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d-m-Y', strtotime($gatePasse->date)) }}</td>
                            <td>{{ $gatePasse->invoice_no }}</td>
                            <td>{{ @$gatePasse->dealer->name }}</td>
                            <td>
                                @php
                    				echo \App\Link::action($gatePasse->id);
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

            var updateThis;

            //ajax delete code
            $('#dataTable tbody').on('click', 'i.fa-trash', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                gatePassId = $(this).parent().data('id');
                // console.log(liftingId);
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
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('gatePass.delete') }}",
                                data: {
                                    id: gatePassId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "GatePass Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('#' + gatePassId).remove();
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
                        } else {
                            swal({
                                title: "Cancelled",
                                type: "error",
                                text: "GatePass Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });
        });
    </script>
@endsection
