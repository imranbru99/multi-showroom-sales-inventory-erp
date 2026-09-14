@extends('admin.layouts.masterIndex')

@section('card_body')
<div class="card-body">
    <div class="table-responsive">
        @php
        $message = Session::get('msg');
        if (isset($message))
        {
        echo"<div style='display:inline-block;width: auto;' class='alert alert-success'><strong>" .$message."</strong></div>";
        }

        Session::forget('msg');
        @endphp
        <table id="dataTable" class="table table-bordered table-striped"  name="usersTable">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Company Name</th>
                    <th>Branch Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbody">
                @foreach($users as $user)                       	
                <tr class="row_{{ $user->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->company->name }}</td>
                    <td>
                        <?php
                        $showrooms = [];
                        if ($user->showroomId) {
                            $showrooms = explode(',', $user->showroomId);
                        }
                        $showrooms = \App\ShowroomSetup::whereIn('id', $showrooms)->get();
                        ?>
                        @foreach($showrooms as $showroom)
                        {{ $showroom->name }} <br>
                        @endforeach
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <?php echo \App\Link::status($user->id, $user->status) ?>
                    </td>
                    <td class="text-nowrap">
                        <?php echo \App\Link::action($user->id) ?>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- The Modal -->
<div id="showUser" class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">User Information</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div id="showContent"></div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        var updateThis;


        //ajax show code
        $('#dataTable tbody').on('click', 'i.fa-eye', function () {
            updateThis = this;
            userId = $(this).parent().data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('user.profile') }}",
                data: {userId: userId},

                success: function (response) {
                    user = response.user;
                    name = response.name;
                    showFunction(user);
                },
                error: function (response) {
                    error = "Something wrong.";
                    swal({
                        title: "<small class='text-danger'>User Not Found</small>",
                        type: "error",
                        text: error,
                        timer: 1000,
                        html: true,
                    });
                }
            });
        });

        //seperate the show function to understand
        function showFunction(user) {
            if (user.status == 1)
                userStatus = `<span class="badge badge-pill badge-success">Active User</span>`;
            else
                userStatus = `<span class="badge badge-pill badge-danger">In-active User</span>`
            var content = `
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th colspan="4"><h4>` + user.name + ` ` + userStatus + `</h4></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th width="10px">Role</th>
                            <td width="5px">:</td>
                            <td>` + user.userRoleName + `</td>
                            <td rowspan="4" width="100px">
                                <img src="{{ url('') }}/` + user.image + `" class="img-thumbnail" alt="User Image" width="100px" height="100px">
                            </td>
                        </tr>
                        <tr>
                            <th width="10px">User Name</th>
                            <td width="5px">:</td>
                            <td>` + user.username + `</td>
                        </tr>
                        <tr>
                            <th width="10px">Showrooms</th>
                            <td width="5px">:</td>
                            <td>` + name + `</td>
                        </tr>
                        <tr>
                            <th width="10px">Email</th>
                            <td width="5px">:</td>
                            <td>` + user.email + `</td>
                        </tr>
                    </tbody>
                </table>
            `;

            $('#showContent').html(content);
            $("#showUser").modal();
        }

        //ajax delete code
        $('#dataTable tbody').on('click', 'i.fa-trash', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            userId = $(this).parent().data('id');
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
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('user.delete') }}",
                                data: {userId: userId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "User Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + userId).remove();
                                },
                                error: function (response) {
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
                        } else
                        {
                            swal({
                                title: "Cancelled",
                                type: "error",
                                text: "Your User is safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });

    });

    //ajax status change code
    function statusChange(user_id, status) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('user.status') }}",
            data: {userId: user_id, status: status},
            success: function (response) {
                swal({
                    title: "<small class='text-success'>Success!</small>",
                    type: "success",
                    text: "Status Successfully Updated!",
                    timer: 1000,
                    html: true,
                });
            },
            error: function (response) {
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