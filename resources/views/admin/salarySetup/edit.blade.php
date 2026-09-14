@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <input type="hidden" name="salarySetupId" value="{{ $salarySetup->id }}">

    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('title') ? ' has-danger' : '' }}">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="{{ $salarySetup->title }}" placeholder="Title">
                @if ($errors->has('title'))
                @foreach($errors->get('title') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('cut_on') ? ' has-danger' : '' }}">
                <label for="cut_on">Salary Cut On</label>
                <input type="number" class="form-control" name="cut_on" value="{{ $salarySetup->cut_on }}" placeholder="3 Days Late">
                @if ($errors->has('cut_on'))
                @foreach($errors->get('cut_on') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('salary_cut') ? ' has-danger' : '' }}">
                <label for="salary_cut">Salary Cut Days</label>
                <input type="number" class="form-control" name="salary_cut" value="{{ $salarySetup->salary_cut }}" required placeholder="1 Day Salary Cut">
                @if ($errors->has('salary_cut'))
                @foreach($errors->get('salary_cut') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

    </div>
</div>
@endsection