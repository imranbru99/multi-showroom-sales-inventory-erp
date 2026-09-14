@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="district">Region</label>
                        <select class="form-control chosen-select" name="districtId" id="districtId">
                            <option value="">Select Region</option>
                            @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="upazila">Area</label>
                    <div class="form-group" id="upazila-select-menu">
                        <select class="form-control chosen-select" name="upazilaId" id="upazilaId">
                            <option value="">Select Area</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <label for="teritori">Teritory</label>
                    <div class="form-group" id="teritori-select-menu">
                        <select class="form-control chosen-select" name="territoryId" id="territoryId">
                            <option value="">Select Teritory</option>
                            @foreach ($territories as $territorie)
                            <option value="{{ $territorie->id }}">{{ $territorie->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    @php
                    $dealerTypes = [
                    'Internal'=>'Internal',
                    'External'=>'External'
                    ];
                    @endphp
                    <div class="form-group {{ $errors->has('dealerType') ? ' has-danger' : '' }}">
                        <label for="dealer-type">Dealer Type</label>
                        <select class="form-control" name="dealerType">
                            <option value="">Select Type</option>
                            @foreach ($dealerTypes as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('dealerType'))
                        @foreach($errors->get('dealerType') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('dealerName') ? ' has-danger' : '' }}">
                <label for="dealer-name">Dealer Name</label>
                <input type="text" class="form-control" name="dealerName" value="{{ old('dealerName') }}" required>
                @if ($errors->has('dealerName'))
                @foreach($errors->get('dealerName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group {{ $errors->has('short_name') ? ' has-danger' : '' }}">
                <label for="short_name">Short Name</label>
                <input type="text" class="form-control" name="short_name" value="{{ old('short_name') }}" maxlength="20" required>
                @if ($errors->has('short_name'))
                @foreach($errors->get('short_name') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                <label for="code">Code</label>
                <input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
                @if ($errors->has('code'))
                @foreach($errors->get('code') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group {{ $errors->has('commission') ? ' has-danger' : '' }}">
                <label for="commission">Commission (%)</label>
                <input type="text" class="form-control" name="commission" value="{{ old('commission') }}" required>
                @if ($errors->has('commission'))
                @foreach($errors->get('commission') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group {{ $errors->has('creditLimit') ? ' has-danger' : '' }}">
                <label for="credit-limit">Credit Limit</label>
                <input type="text" class="form-control" name="creditLimit" value="{{ old('creditLimit') }}" required>
                @if ($errors->has('creditLimit'))
                @foreach($errors->get('creditLimit') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('contactPerson') ? ' has-danger' : '' }}">
                        <label for="contact-person">Contact Person</label>
                        <input type="text" class="form-control" name="contactPerson" value="{{ old('contactPerson') }}"
                            required>
                        @if ($errors->has('contactPerson'))
                        @foreach($errors->get('contactPerson') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('mobile') ? ' has-danger' : '' }}">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" required>
                        @if ($errors->has('mobile'))
                        @foreach($errors->get('mobile') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                        @foreach($errors->get('email') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">

            <div class="row">

                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                        @if ($errors->has('address'))
                        @foreach($errors->get('address') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('courier_address') ? ' has-danger' : '' }}">
                        <label for="courier_address">Courier Address</label>
                        <input type="text" class="form-control" name="courier_address"
                            value="{{ old('courier_address') }}" required>
                        @if ($errors->has('courier_address'))
                        @foreach($errors->get('courier_address') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@section('custom-js')

<script type="text/javascript">
    $(document).on('change', '#districtId', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var districtId = $('#districtId').val();

            $.ajax({
                type:'post',
                url:'{{ route('dealerSetup.getAllUpazilaByDistrict') }}',
                data:{districtId:districtId},
                success:function(data){
                    $('#upazila-select-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        });
        $(document).on('change', '#upazilaId', function(){
        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var upazilaId = $('#upazilaId').val();

            $.ajax({
                type:'post',
                url:'{{ route('dealerSetup.getAllTeritoryByArea') }}',
                data:{upazilaId:upazilaId},
                success:function(data){
                    $('#teritori-select-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        });

</script>




@endsection