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

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                <div class="col-md-6 text-right">
                	<a class="btn btn-outline-info btn-lg" href="{{ route('coaList.print') }}" target="_blank"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped" name="debitTable">
                	<thead>
                		<tr>
                			<th>Head Code</th>
                			<th>Head Name</th>
                		</tr>
                	</thead>

                    <tbody>
                        @foreach ($coaLists as $coaList)
                            <tr>
                                <td>{{ $coaList->head_code }}</td>
                                <td>{{ $coaList->head_name }}</td>
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
    </script>
@endsection