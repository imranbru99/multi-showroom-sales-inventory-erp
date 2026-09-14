<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">

        <title>Admin || {{ $title }}</title>

        <link rel="icon" type="image/png" sizes="20x20" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">

        <style type="text/css">
            .card-pad{
                padding-bottom: 10px;
            }
        </style>
        @yield('custom_css')

        @include('admin.partials.header-assets')
    </head>
    <body class="skin-default fixed-layout">
        <!-- Preloader - style you can find in spinners.css -->
        @include('admin.partials.preloader')
        <!-- Main wrapper - style you can find in pages.scss -->
        <div id="main-wrapper">
            <!-- Topbar header - style you can find in pages.scss -->
            <header class="topbar">
                @include('admin.partials.top-navbar')
            </header>
            <!-- End Topbar header -->

            <!-- Left Sidebar - style you can find in sidebar.scss  -->       
            @include('admin.partials.menu')
            <!-- End Left Sidebar - style you can find in sidebar.scss  -->

            <!-- Page wrapper  -->
            <div class="page-wrapper">
                <!-- Container fluid  -->
                <div class="container-fluid">
                    <!-- Bread crumb and right sidebar toggle -->
                    @yield('bread-crumb')
                    <!-- End Bread crumb and right sidebar toggle -->

                    <div style="padding-bottom: 30px;"></div>

                    <!-- Page Content -->
                    @yield('content')
                    <!-- End Page Content -->

                    <!-- Right sidebar -->
                    @include('admin.partials.right-sidebar')
                    <!-- End Right sidebar -->
                </div>
                <!-- End Container fluid  -->
            </div>
            <!-- End Page wrapper  -->

            <!-- footer -->
            <footer class="footer">
                © {{ date('Y') }} Developed by <a target="_blank" href="http://www.technoparkbd.com/">Techno Park Bangladesh</a>
            </footer>
            <!-- End footer -->

        </div>
        <!-- End Wrapper -->

        <style type="text/css">
            @media screen and (max-device-width: 991px) {

                .showroom-view {
                    font-size: 14px !important;
                }
            }

            @media (max-width: 767px){
                .mini-sidebar .left-sidebar, .mini-sidebar .sidebar-footer {
                    left: -260px; 
                }

                .add_new,
                .buttonAddEdit{
                    font-size: 14px !important;
                }
            }

        </style>

        @include('admin.partials.footer-assets')

        <!-- This page plugins -->
        @yield('custom-js')

    </body>
</html>