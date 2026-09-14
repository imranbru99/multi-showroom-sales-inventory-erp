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
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/uploads/admin_logo/small_logo.png') }}">

        <title>Admin || {{ $title }}</title>

        <link rel="icon" type="image/png" sizes="20x20" href="{{ asset('public/uploads/admin_logo/small_logo.png') }}">

        <style type="text/css">
            .card-pad {
                padding-bottom: 10px;
            }

            @media screen and (max-device-width: 991px) {

                .showroom-view {
                    font-size: 14px !important;
                }
            }

            /*            @media screen and (min-device-width: 768px) and (max-device-width: 991px) {
            
                            .showroom-view {
                                font-size: 14px !important;
                            }
                        }*/

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

                    @php
                    $message = Session::get('msg');
                    @endphp

                    @if (isset($message))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Success!</strong> {{ $message }}
                    </div>
                    @endif


                    @php
                    $message = Session::get('fail_msg');
                    @endphp

                    @if (isset($message))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Failed!</strong> {{ $message }}
                    </div>
                    @endif


                    @php
                    Session::forget('msg');
                    @endphp

                    @if (count($errors) > 0)
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Oops!</strong> {{ $errors->first() }}
                    </div>
                    @endif

                    <div class="modal fade bd-example-modal-lg text-left" tabindex="-1" role="dialog"
                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">

                            @if (\Route::current()->getName() == 'retailSales.add')
                            <form action="{{ route('customerRegistration.save') }}" method="post">
                                {{ csrf_field() }}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Customer</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="text-center"
                                                        style="font-weight: bold;font-family: tahoma">
                                                        Personal Information</h4>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div
                                                        class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        <label for="name">Applicant's Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                               value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                        @foreach ($errors->get('name') as $error)
                                                        <div class="form-control-feedback">{{ $error }}
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                                                        <label for="code">Applicant's Code</label>
                                                        <input type="text" class="form-control" id="apllicantsCode"
                                                               name="code" value="{{ $apllicantsCode }}">
                                                        @if ($errors->has('code'))
                                                        @foreach ($errors->get('code') as $error)
                                                        <div class="form-control-feedback">{{ $error }}
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="form-group {{ $errors->has('phoneNo') ? ' has-danger' : '' }}">
                                                        <label for="phone-no">Phone No</label>
                                                        <input type="text" class="form-control"
                                                               id="applicants_phone_no" name="phoneNo"
                                                               value="{{ old('phoneNo') }}" oninput="ApplicantCode()">
                                                        @if ($errors->has('phoneNo'))
                                                        @foreach ($errors->get('phoneNo') as $error)
                                                        <div class="form-control-feedback">{{ $error }}
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div
                                                        class="form-group {{ $errors->has('presentAddress') ? ' has-danger' : '' }}">
                                                        <label for="present-address">Present Address</label>
                                                        <textarea class="form-control" name="presentAddress"
                                                                  rows="2">{{ old('presentAddress') }}</textarea>
                                                        @if ($errors->has('presentAddress'))
                                                        @foreach ($errors->get('presentAddress') as $error)
                                                        <div class="form-control-feedback">{{ $error }}
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div
                                                        class="form-group {{ $errors->has('permanentAddress') ? ' has-danger' : '' }}">
                                                        <label for="permanent-address">Permanent Address</label>
                                                        <textarea class="form-control" name="permanentAddress"
                                                                  rows="2">{{ old('permanentAddress') }}</textarea>
                                                        @if ($errors->has('permanentAddress'))
                                                        @foreach ($errors->get('permanentAddress') as $error)
                                                        <div class="form-control-feedback">{{ $error }}
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-info">Save</button>
                                        <button type="button" class="btn btn-outline-danger"
                                                data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </form>
                            @endif

                        </div>
                    </div>

                    <form class="form-horizontal" action="{{ route($formLink) }}" id="formAddEdit" method="POST"
                          enctype="multipart/form-data" name="form">
                        {{ csrf_field() }}

                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">{{ $title }}</h4>
                                    </div>
                                    <div class="col-md-6 text-right">

                                        @if (\Route::current()->getName() == 'retailSales.add')
                                        <a class="btn btn-outline-info btn-lg text-info" data-toggle="modal"
                                           data-target=".bd-example-modal-lg">
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            Add New Customer
                                        </a>

                                        @endif

                                        <a class="btn btn-outline-info btn-lg go_back" href="{{ route($goBackLink) }}">
                                            <i class="fa fa-arrow-circle-left"></i> Go Back
                                        </a>
                                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect buttonAddEdit"
                                                name="buttonAddEdit" value="Save"><i class="fa fa-save"></i>
                                            {{ $buttonName }}</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body Content -->
                            @yield('card_body')
                            <!-- End Card Body Content -->

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect buttonAddEdit"
                                                name="buttonAddEdit" value="Save"><i class="fa fa-save"></i>
                                            {{ $buttonName }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Right sidebar -->
                    @include('admin.partials.right-sidebar')
                    <!-- End Right sidebar -->
                </div>
                <!-- End Container fluid  -->
            </div>
            <!-- End Page wrapper  -->

            <!-- footer -->
            <footer class="footer">
                © {{ date('Y') }} Developed by <a target="_blank" href="http://www.technoparkbd.com/">Techno Park
                    Bangladesh</a>
            </footer>
            <!-- End footer -->

            <style type="text/css">
                .card-pad {
                    padding-bottom: 10px;
                }

                @media screen and (max-device-width: 991px) {

                    .showroom-view {
                        font-size: 14px !important;
                    }
                }

                @media (max-width: 767px){
                    .mini-sidebar .left-sidebar, .mini-sidebar .sidebar-footer {
                        left: -260px; 
                    }

                    .go_back,
                    .buttonAddEdit{
                        font-size: 14px;
                    }
                }

                /*            @media screen and (min-device-width: 768px) and (max-device-width: 991px) {
                
                                .showroom-view {
                                    font-size: 14px !important;
                                }
                            }*/

            </style>

        </div>
        <!-- End Wrapper -->



        @include('admin.partials.footer-assets')

<!--        <script>
            $(function () {
                $(".buttonAddEdit").click(function () {
                    $(".buttonAddEdit").attr("disabled", true);
                    $('#formAddEdit').submit();
                });
            });
        </script>-->

        <!-- This page plugins -->
        @yield('custom-js')

    </body>

</html>
