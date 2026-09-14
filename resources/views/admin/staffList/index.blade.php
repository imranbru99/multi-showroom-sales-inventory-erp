@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <label for="branch">Branch</label>
        <div class="form-group">
            <select class="form-control chosen-select" name="branch[]" multiple>
                @foreach ($branches as $branc)
                <?php
                $select = "";
                if ($branch) {
                    if (in_array($branc->id, $branch)) {
                        $select = "selected";
                    }
                }
                ?>
                <option value="{{ $branc->id }}" {{ $select }}>{{ $branc->name }}</option>
                @endforeach
            </select>
        </div>  
    </div>
</div>	
@endsection

@section('print_card_header')
@if ($branch)
@foreach ($branch as $b)
<input type="hidden" name="branch[]" value="{{ $b }}">
@endforeach
@endif

<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<div class="table-responsive">
    <table id="dataTable" name="productList" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Contact</th>
                <th>Address</th>
            </tr>
        </thead>

        <tbody>
            @foreach($staffs as $staff)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $staff->code }}</td>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->designation }}</td>
                <td>{{ $staff->contact }}</td>
                <td>{{ $staff->address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
