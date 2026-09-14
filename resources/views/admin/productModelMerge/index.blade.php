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
        <link rel="icon" type="image/png" sizes="16x16"
            href="{{ asset('/') }}public/uploads/admin_logo/logo_small.jpg">

        <title>Admin || {{ $title }}</title>

        <link rel="icon" type="image/png" sizes="20x20"
            href="{{ asset('/') }}public/uploads/admin_logo/logo_small.jpg">

        <style type="text/css">
            .card-pad {
                padding-bottom: 10px;
            }

            th {
                background-color: #00c292;
                font-weight: bold !important;
            }

            .search_report {
                display: none;
            }

            .search {
                display: none;
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

                    <div style="padding-bottom: 10px;"></div>

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


                    <form class="form-horizontal" id="searchForm" action="{{ route($searchFormLink) }}" method="POST"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">{{ $title }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <input type="hidden" value="true" name="searched">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-6">
                                        <label for="model">Product Model</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="productModel" value="{{ @$productModel }}"placeholder="Model No">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" id="search"
                                            class="btn btn-outline-info btn-lg waves-effect search"><i
                                                class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card search_report" style="margin-bottom: 0px;">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="card-title">Search Report</h4>
                                </div>
                                <div class="col-md-12 text-right">
                                    <div class="card show-class" style="margin-bottom: 0px;">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="card-title">Search Report</h4>
                                                </div>
                                                <div class="col-md-12 text-right">
                                                    <form class="form-horizontal"
                                                        action="{{ route($printFormLink) }}" method="post"
                                                        enctype="multipart/form-data">
                                                        {{ csrf_field() }}

                                                        <table id="" name="paymentRecordTable"
                                                            class="table table-bordered table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">SL#</th>
                                                                    <th class="text-left">Product Name</th>
                                                                    <th class="text-left">Product Model</th>
                                                                    <th class="text-center">Action</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                @php
                                                                    $i = 1;
                                                                @endphp
                                                                @foreach ($data as $product)
                                                                    <tr>
                                                                        <td align="center">{{ $i++ }}</td>
                                                                        <td align="left">{{ $product->name }}</td>
                                                                        <td align="left">{{ $product->model_no }}</td>
                                                                        <td align="center">
                                                                            <input type="checkbox"
                                                                                name="selected_models[]"
                                                                                value="{{ $product->id }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <button type="submit"
                                                            class="btn btn-outline-info btn-lg waves-effect"><i
                                                                class="fa fa-spinner" aria-hidden="true"></i>
                                                            Process</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Print Card Body Content -->
                            @yield('print_card_body')
                            <!-- End Print Card Body Content -->
                        </div>
                    </div>

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

        </div>
        <!-- End Wrapper -->



        @include('admin.partials.footer-assets')

        <script>
            $('#print').submit(function() {
                if ($('#print_value').val() == "") {
                    swal("Please! Search Data", "", "warning");
                    return false;
                }
            });
        </script> -->

        <!-- This page plugins -->
        @yield('custom-js')

    </body>

    </html>
