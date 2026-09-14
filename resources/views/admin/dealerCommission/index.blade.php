@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped" name="areaTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Dealer Name</th>
                        <th width="150px">Status</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @foreach ($commissions as $commission)
                        <tr class="row_{{ $commission->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ @$commission->dealer->name }}</td>
                            <td>
                                <?php echo \App\Link::status($commission->id, $commission->status); ?>
                            </td>
                            <td>
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

                commissionId = $(this).parent().data('id');

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
                                url: "{{ route('dealerCommission.delete') }}",
                                data: {
                                    id: commissionId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Dealer Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + commissionId).remove();
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
                                text: "Your Dealer Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });
        });

        //ajax status change code
        function statusChange(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('dealerCommission.status') }}",
                data: {
                    id: id
                },
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
