@extends('admin.layouts.master')

@section('title')
    <title>{{ $title }}</title>
@endsection

@section('custom-css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-name')
Add Mobile
@endsection

@section('content')

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                </div>
            </div>

            <div class="card-body">
                @php
                    $message = Session::get('msg');
                      if (isset($message))
                      {
                        echo"<div style='display:inline-block;width: auto;' class='alert alert-success'><strong>" .$message."</strong></div>";
                      }
                      Session::forget('msg')                    
                @endphp
                <form class="form-horizontal" action="{{route('adminLogo.update')}}" method="POST" enctype="multipart/form-data" name="editlogos">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group {{ $errors->has('adminLogo') ? ' has-danger' : '' }}">
                                <input type="file" class="form-control" id="adminLogo" aria-describedby="fileHelp" name="adminLogo">
                                @if ($errors->has('adminLogo'))
                                    @foreach($errors->get('adminLogo') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2" align="center">                            
                            <img src="{{ asset('/').@$logos->adminLogo }}" style="width:140px;">
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="submit" class="btn btn-outline-info waves-effect">Update Information</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-dialog -->
            </div>
        </div>
    </div>
</div>

@endsection

