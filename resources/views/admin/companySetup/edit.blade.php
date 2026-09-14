@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }
</style>

<div class="card-body">

    <input type="hidden" name="companyId" value="{{ $company->id }}">

    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('prefix') ? ' has-danger' : '' }}">
                <label for="prefix">prefix</label>
                <input type="text" class="form-control" name="prefix" value="{{ $company->prefix }}" required>
                @if ($errors->has('prefix'))
                @foreach ($errors->get('prefix') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('companyName') ? ' has-danger' : '' }}">
                <label for="company-name">Company Name</label>
                <input type="text" class="form-control" name="companyName" value="{{ $company->name }}" required>
                @if ($errors->has('companyName'))
                @foreach ($errors->get('companyName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group ">
                <label for="companyProductTypes">Product Type</label>
                <select class="form-control chosen-select" id="companyProductTypes" name="companyProductTypes[]" multiple>

                    <option value="warranty_product" @if (in_array('warranty_product', json_decode($company->product_types)))
                        selected
                    @endif>Warranty Product</option>

                    <option value="consumer_product"@if (in_array('consumer_product', json_decode($company->product_types)))
                        selected
                    @endif>Consumer Product</option>

                    <option value="spare_parts" @if (in_array('spare_parts', json_decode($company->product_types)))
                        selected
                    @endif>Spare Parts</option>

                    <option value="raw_product" @if (in_array('raw_product', json_decode($company->product_types)))
                        selected
                    @endif>Raw Product</option>
                </select>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('tradeLicense') ? ' has-danger' : '' }}">
                <label for="trade-license">Trade License</label>
                <input type="text" class="form-control" name="tradeLicense" value="{{ $company->trade_license }}"
                    required>
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
                        <input type="text" class="form-control" name="vat" value="{{ $company->vat }}" required>
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
                        <input type="text" class="form-control" name="tin" value="{{ $company->tin }}" required>
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
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ $company->email }}" required>
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
                        <input type="text" class="form-control" name="webSite" value="{{ $company->website }}" required>
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
                        <input type="text" class="form-control" name="phoneNumber" value="{{ $company->phone }}"
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
                        <input type="text" class="form-control" name="faxNumber" value="{{ $company->fax }}" required>
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
                <textarea class="form-control" name="address" rows="5">{{ $company->address }}</textarea>
                @if ($errors->has('address'))
                @foreach ($errors->get('address') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@if (auth()->user()->role == 1)
<div class="card-body">
    <h4><u>Module List</u></h4>
    <div class="border p-3" style="background-color: beige">
        <div class="row">
            @foreach ($menus as $menu)
            <?php
                        $checked = '';
                        if (in_array($menu->id, explode(',', $company->modules))) {
                            $checked = 'checked';
                        }
                        ?>
            <div class="col-md-3">
                <input type="checkbox" name="modules[]" value="{{ $menu->id }}" {{ $checked }}>
                {{ $menu->menuName }}
            </div>
            @endforeach
        </div>
    </div>
</div>
@else
<div class="card-body">
    <h4><u>Module List</u></h4>
    <div class="border p-3" style="background-color: beige">
        <div class="row">
            @foreach ($pMenus as $pMenu)
            <div class="col-md-3">
                <input type="checkbox" checked disabled>
                {{ $pMenu->menuName }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endsection
