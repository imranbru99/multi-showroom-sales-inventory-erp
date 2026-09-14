@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="modal-body">
            <div class="row">
                @if(auth()->user()->role == 1)
                <div class="col-md-6">
                    <label for="company">Company Name</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" name="company" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="@if(auth()->user()->role == 1) col-md-6 @else  col-md-12 @endif">
                    <label for="name">Name</label>
                    <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" placeholder="Name" name="name"
                            value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            @foreach ($errors->get('name') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
