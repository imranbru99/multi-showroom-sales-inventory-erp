@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('prefix') ? ' has-danger' : '' }}">
                <label for="prefix">Branch Prefix</label>
                <input type="text" class="form-control" name="prefix" value="{{ old('prefix') }}" required>
                @if ($errors->has('prefix'))
                @foreach ($errors->get('prefix') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-5">
            <div class="form-group {{ $errors->has('showroomName') ? ' has-danger' : '' }}">
                <label for="showroom-name">Branch Name</label>
                <input type="text" class="form-control" name="showroomName" value="{{ old('showroomName') }}"
                       required>
                @if ($errors->has('showroomName'))
                @foreach ($errors->get('showroomName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        @if (auth()->user()->role == 1)
        <div class="col-md-5">
            <div class="form-group {{ $errors->has('company') ? ' has-danger' : '' }}">
                <label for="company">prefix</label>
                <select name="company" id="company" class="form-control chosen-select">
                    <option value="">Select Company</option>
                    @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('prefix'))
                @foreach ($errors->get('company') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        @else
        <div class="col-md-5">
            <div class="form-group {{ $errors->has('company') ? ' has-danger' : '' }}">
                <label for="company">Company Name</label>
                <input type="text" class="form-control" name="company" value="{{ $companies->name }}" readonly>
                @if ($errors->has('prefix'))
                @foreach ($errors->get('company') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('tradeLicense') ? ' has-danger' : '' }}">
                <label for="trade-license">Trade License</label>
                <input type="text" class="form-control" name="tradeLicense" value="{{ old('tradeLicense') }}">
                @if ($errors->has('tradeLicense'))
                @foreach ($errors->get('tradeLicense') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('vat') ? ' has-danger' : '' }}">
                        <label for="vat">VAT</label>
                        <input type="text" class="form-control" name="vat" value="{{ old('vat') }}">
                        @if ($errors->has('vat'))
                        @foreach ($errors->get('vat') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('tin') ? ' has-danger' : '' }}">
                        <label for="tin">TIN</label>
                        <input type="text" class="form-control" name="tin" value="{{ old('tin') }}">
                        @if ($errors->has('tin'))
                        @foreach ($errors->get('tin') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('contactPerson') ? ' has-danger' : '' }}">
                        <label for="contact-person">Contact Person</label>
                        <input type="text" class="form-control" name="contactPerson"
                               value="{{ old('contactPerson') }}" required>
                        @if ($errors->has('contactPerson'))
                        @foreach ($errors->get('contactPerson') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                        @foreach ($errors->get('email') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('webSite') ? ' has-danger' : '' }}">
                        <label for="web-site">Web Site</label>
                        <input type="text" class="form-control" name="webSite" value="{{ old('webSite') }}">
                        @if ($errors->has('webSite'))
                        @foreach ($errors->get('webSite') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('phoneNumber') ? ' has-danger' : '' }}">
                        <label for="phone-no">Phone No.</label>
                        <input type="text" class="form-control" name="phoneNumber" value="{{ old('phoneNumber') }}"
                               required>
                        @if ($errors->has('phoneNumber'))
                        @foreach ($errors->get('phoneNumber') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('faxNumber') ? ' has-danger' : '' }}">
                        <label for="fax-no">Fax No.</label>
                        <input type="text" class="form-control" name="faxNumber" value="{{ old('faxNumber') }}">
                        @if ($errors->has('faxNumber'))
                        @foreach ($errors->get('faxNumber') as $error)
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
                <textarea class="form-control" name="address" rows="9">{{ old('address') }}</textarea>
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

@section('custom-js')
<script src="{{ asset('/public/admin-elite/assets/node_modules/datatables/jquery.dataTables.min.js') }}"></script>
@endsection
