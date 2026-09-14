<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" sizes="20x20" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">
    <title>{{ $title }}</title>
    <style>
        .bg-grin {
            background: #00c292;
        }

        #report-table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #report-table td,
        #report-table th {
            border: 1px solid #ddd;
        }

        #report-table td {
            font-size: 11px;
            padding: 5px;
        }

        #report-table th {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            font-size: 13px;
            vertical-align: middle;
            padding: 3px;
        }

        #report-header {
            background-color: lightgray;
            width: 100%;
            padding: 0px;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
        }

        .align-left {
            text-align: left;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        #report-table tfoot th {
            background-color: #4CAF50;
            color: white;
            text-align: right;
            font-size: 11px !important;
        }

        #report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #report-table tr:hover {
            background-color: #ddd;
        }

        caption {
            font-weight: bold;
            text-decoration: underline;
            padding-bottom: 5px;
        }

        #header-table {
            font-family: Times, "Times New Roman", serif;
            border-collapse: collapse;
            width: 100%;
        }

        #header-table td,
        #header-table th {
            text-align: center;
            /* background-color: #a4b7b4; */
            color: black;
            border: 0px solid #ddd;
            padding: 5px;
        }

        #header-table td {
            font-size: 14px;
        }

        #header-table th {
            font-size: 20px;
        }

        #pad-bottom {
            padding-bottom: 10px;
        }

        .overline {
            border-top: 2px solid currentColor;
        }


        #account-voucher-table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #account-voucher-table td,
        #account-voucher-table th {
            border: 1px solid #ddd;
        }

        #account-voucher-table td {
            font-size: 11px;
            padding: 5px;
        }

        #account-voucher-table th {
            text-align: center;
            /*background-color: #4CAF50;*/
            color: black;
            font-size: 13px;
            vertical-align: middle;
            padding: 3px;
        }

        #account-voucher-header {
            /*background-color: lightgray;*/
            border: 1px solid #ddd;
            width: 100%;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
        }

        #account-voucher-table tfoot th {
            color: black;
        }

        #voucher-sign-table {
            width: 100%;
            border-collapse: collapse;
        }

        #voucher-sign-table td {
            width: 50%;
            border: 0px solid black;
        }
    </style>

    @yield('custome-css')
</head>

<body align="center">
    @php
    error_reporting(0);

    // dd(session('companyInfo'));
    // use App\CompanySetup;

    $company = session('companyInfo');
    @endphp

    <table id="header-table">
        <thead>
            <tr class="bg-grin">
                <th>{{ $company->name }}</th>
            </tr>
            <tr>
                <td>
                    <b>Address:</b> {!! $company->address !!}
                    <br>
                    <b>Phone:</b> {{ $company->phone }}
                    <br>
                    <b>Website:</b> {{ $company->website }}, <b>Email: </b>{{ $company->email }}
                </td>
            </tr>
            {{-- <tr>
                <td>
                    <b>Phone:</b> {{ $company->phone }}
                    <br>
                    <b>Website:</b> {{ $company->website }}, <b>Email: </b>{{ $company->email }}
                </td>
            </tr> --}}
        </thead>
    </table>

    <hr>

    @yield('content')
</body>

</html>
