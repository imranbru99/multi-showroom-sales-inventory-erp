@extends('admin.layouts.masterAddEdit')

@section('custom_css')
<style type="text/css">
    #total-target {
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row">
        <input type="hidden" name="id" class="form-control" value="{{ $campaign->id }}">
        <div class="col-md-6">
            <div class="form-group">
                <label>Employee</label>
                <select class="form-control chosen-select" name="employee" required>
                    <option value="">Select Employee</option>
                    @foreach($staffs as $staff)
                    <option value="{{ $staff->id }}" @if($campaign->employee_id == $staff->id) selected @endif>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Commission</label>
                <input type="text" name="commission" class="form-control" value="{{ $campaign->commission }}" required>
            </div>
        </div>
    </div>
</div>
@endsection
