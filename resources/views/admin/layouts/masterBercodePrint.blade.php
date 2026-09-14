<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/png" sizes="20x20" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">
        <title>{{ $title }}</title>
        <style>
            #report-table {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
            @page {
                margin: 50px 45px 30px;
            }
            /** Define now the real margins of every page in the PDF **/
            header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                height: 40px;

                /** Extra personal styles **/
                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed; 
                bottom: -60px; 
                left: 0px; 
                right: 0px;
                height: 30px; 

                /** Extra personal styles **/
                color: white;
                text-align: center;
                line-height: 30px;
            }
            .col-md-4{
                width: 33.3333%;
                float: left;
                height: 80px;
                text-align: center;
              	margin-bottom: 20px;
            }
            .col-md-4::nth-of-type(2n+2){
                padding-right: 15px;
            }
            .col-md-4 p{ font-size: 12px; margin-bottom: 2px;}
        </style>

        @yield('custome-css')
    </head>

    @php
    @endphp

    <body align="center">
    	@php
    		use App\CompanySetup;

    		$company = CompanySetup::first();
    	@endphp
        <header> </header>

        <footer></footer>
    	@yield('content')
    </body>
</html>