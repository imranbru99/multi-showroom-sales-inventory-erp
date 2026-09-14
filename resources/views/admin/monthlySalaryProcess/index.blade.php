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
<?php
Session::forget('msg');
$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July ',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];
if($month){
    $month = $month;
}else{
    $month = date('m');
}

if($year){
    $year = $year;
}else{
    $year = date('Y');
}
?>

<div class="card">   
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
        </div>
    </div>
    <form action="{{ route('monthlySalaryProcess.index') }}" method="post">        
        <div class="card-body">
            {{ csrf_field() }}
            <div class="row d-flex justify-content-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Month</label>
                        <!--<input type="text" class="form-control month_process" name="month">-->
                        <select class="form-control" name="month">
                            <?php
                            foreach ($months as $key => $value) {
                                $inc_month = str_pad($key, 2, '0', STR_PAD_LEFT);
                                $select = '';
                                if ($inc_month == $month) {
                                    $select = 'selected';
                                }

                                echo '<option value="' . $inc_month . '" ' . $select . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Year</label>
                        <!--<input type="month" class="form-control" name="month">-->
                        <select class="form-control" name="year">
                            <?php
                            for ($i = 2015; $i <= 2030; $i++) {
                                $select = '';
                                if ($i == $year) {
                                    $select = 'selected';
                                }
                                echo '<option value="' . $i . '"' . $select . '>' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Company</label>
                        <!--<input type="month" class="form-control" name="month">-->
                        <select class="form-control" name="company" required>
                            <?php
                            foreach ($companies as $comp) {
                                $select = '';
                                if ($comp->id == $company) {
                                    $select = 'selected';
                                }
                                echo '<option value="' . $comp->id . '"' . $select . '>' . $comp->name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-info btn-lg mt-4"><i class="fa fa-spinner"></i> Process</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }} List</h4></div>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" name="monthlySalaryProcess" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Company Name</th>
                    <th width='120px'>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlySalaryProcess as $monthlySalaryProc)
                <tr id="row_{{ $monthlySalaryProc->id }}">
                    <td></td>
                    <td>{{ date('F', strtotime($monthlySalaryProc->month_year)) }}</td>
                    <td>{{ date('Y', strtotime($monthlySalaryProc->month_year)) }}</td>
                    <td>{{ @$monthlySalaryProc->showroom->name }}</td>
                    <td>
                        @php
                        echo \App\Link::action($monthlySalaryProc->id);
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
    $(document).ready(function () {
        var updateThis;

        //ajax delete code
        $('#dataTable tbody').on('click', 'i.fa-trash', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            salaryProcess = $(this).parent().data('id');
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
                                url: "{{ route('monthlySalaryProcess.delete') }}",
                                data: {salaryProcess: salaryProcess},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Data Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('#row_' + salaryProcess).remove();
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
                                text: "Your Data Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });
</script>
@endsection