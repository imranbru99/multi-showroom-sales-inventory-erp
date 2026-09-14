@extends('admin.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">{{ $title }}</h4>
                </div>
                <div class="col-md-6">
                    <span class="shortlink">
                        <a style="font-size: 16px;" class="btn btn-outline-info btn-lg"
                            href="{{ route('companySetup.edit', $company->id) }}">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-borderless table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="6">Company Information</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- @foreach ($allCompany as $company) --}}
                    <tr>
                        <th width="100px">Name</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->name }}</td>
                    </tr>
                    <th width="100px">Web Site</th>
                    <td width="20px">:</td>
                    <td>{{ @$company->website }}</td>
                    <tr>
                        <th width="100px">Phone</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->phone }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Email</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->email }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Fax</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->fax }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Vat</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->vat }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Tin</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->tin }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Trade License</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->trade_license }}</td>
                    </tr>
                    <tr>
                        <th width="100px">Address</th>
                        <td width="20px">:</td>
                        <td>{{ @$company->address }}</td>
                    </tr>
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>

        <div class="card-footer"></div>
    </div>
@endsection
