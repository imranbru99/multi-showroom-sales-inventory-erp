@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <input type="hidden" name="staffId" value="{{ $staff->id }}">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                <label for="code-prefix">Code</label>
                <input type="text" class="form-control" name="code" value="{{ $staff->code }}" required>
                @if ($errors->has('code'))
                @foreach ($errors->get('code') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('staffName') ? ' has-danger' : '' }}">
                <label for="staff-name">Staff Name</label>
                <input type="text" class="form-control" name="staffName" value="{{ $staff->name }}" required>
                @if ($errors->has('staffName'))
                @foreach ($errors->get('staffName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('designation') ? ' has-danger' : '' }}">
                <label for="designation">Designation</label>
                <input type="text" class="form-control" name="designation" value="{{ $staff->designation }}" required>
                @if ($errors->has('designation'))
                @foreach ($errors->get('designation') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('short_name') ? ' has-danger' : '' }}">
                <label for="short_name">Display Name</label>
                <input type="text" class="form-control" name="short_name" value="{{ $staff->short_name }}"
                       maxlength="20" required>
                @if ($errors->has('short_name'))
                @foreach ($errors->get('short_name') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>



        <div class="col-md-8">
            <div class="form-group {{ $errors->has('branch') ? ' has-danger' : '' }}">
                <label for="branch">Branch Name</label>
                <select class="form-control chosen-select" name="branch[]" multiple>
                    <option value=''>Select Branch</option>
                    @foreach($branches as $branch)
                    <?php
                        $select = '';
                        if(in_array($branch->id, explode(',', $staff->showroom_id))){
                           $select = 'selected'; 
                        }
                    ?>
                    <option value="{{ $branch->id }}" {{ $select }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('branch'))
                @foreach ($errors->get('branch') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('joiningDate') ? ' has-danger' : '' }}">
                <label for="joining-date">Joining Date</label>
                <input type="text" class="form-control datepicker" id="joiningDate" name="joiningDate"
                       value="{{ date('d-m-Y', strtotime($staff->joining_date)) }}" readonly>
                @if ($errors->has('joiningDate'))
                @foreach ($errors->get('joiningDate') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="ac_no">A/C No</label>
                <input type="text" class="form-control" name="ac_no" placeholder="A/C No"
                       value="{{ $staff->ac_no }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ac_branch">A/C Branch</label>
                <input type="text" class="form-control" name="ac_branch" placeholder="A/C Branch"
                       value="{{ $staff->ac_branch }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('nationalId') ? ' has-danger' : '' }}">
                        <label for="national-id">National Id</label>
                        <input type="text" class="form-control" name="nationalId" value="{{ $staff->national_id }}"
                               required>
                        @if ($errors->has('nationalId'))
                        @foreach ($errors->get('nationalId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('contact') ? ' has-danger' : '' }}">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" class="form-control" name="contact" value="{{ $staff->contact }}"
                               required>
                        @if ($errors->has('contact'))
                        @foreach ($errors->get('contact') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ $staff->email }}" required>
                        @if ($errors->has('email'))
                        @foreach ($errors->get('email') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                <label for="address">Address</label>
                <textarea class="form-control" name="address" rows="5">{{ $staff->address }}</textarea>
                @if ($errors->has('address'))
                @foreach ($errors->get('address') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
