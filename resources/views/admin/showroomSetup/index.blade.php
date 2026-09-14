@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped" name="showroomTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th width="180px">Company Name</th>
                        <th width="180px">Branch Name</th>
                        <th width="180px">Contact Person</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th width="20px">Status</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach ($showrooms as $showroom)
                        @php
                            $sl++;
                        @endphp
                        <tr class="row_{{ $showroom->id }}">
                            <td>{{ $sl }}</td>
                            <td>{{ @$showroom->company->name }}</td>
                            <td>{{ $showroom->name }}</td>
                            <td>{{ $showroom->contact_person }}</td>
                            <td>{{ $showroom->phone }}</td>
                            <td>{{ $showroom->address }}</td>
                            <td>
                                <?php echo \App\Link::status($showroom->id, $showroom->status); ?>
                            </td>
                            <td>
                                @php
                                    echo \App\Link::action($showroom->id);
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

                showroomId = $(this).parent().data('id');
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
                                url: "{{ route('showroomSetup.delete') }}",
                                data: {
                                    showroomId: showroomId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Showroom Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + showroomId).remove();
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
                                text: "Your Showrrom is safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });

        });

        //ajax status change code
        function statusChange(showroomId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('showroomSetup.status') }}",
                data: {
                    showroomId: showroomId
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
