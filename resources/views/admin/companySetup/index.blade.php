@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6">
                <span class="shortlink">
                    <a style="font-size: 16px;" class="btn btn-outline-info btn-lg add_new"
                       href="{{ route('companySetup.add') }}">
                        <i class="fa fa-plus-circle"></i> Add New
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-borderless table-sm">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Company Name</th>
                        <th>Website</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>Auth Username</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->website }}</td>
                        <td>{{ $company->phone }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->auth_username }}</td>
                        <td>
                            <?php echo \App\Link::status($company->id, $company->status); ?>
                        </td>
                        <td>
                            @php
                            echo \App\Link::action($company->id);
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
    //ajax status change code
    function statusChange(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('companySetup.status') }}",
            data: {
                id: id
            },
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
