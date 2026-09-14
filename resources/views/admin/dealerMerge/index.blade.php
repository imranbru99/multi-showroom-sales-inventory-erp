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


                    <form class="form-horizontal" id="searchForm" action="{{ route('dealerMerge.save') }}" method="POST"
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
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="dealer">Dealer</label>
                                            <select class="form-control chosen-select" name="dealer" id="dealer"
                                                required>
                                                <option value="">Select Dealer</option>
                                                @foreach ($dealers as $dealer)
                                                    <option value="{{ $dealer->id }}">{{ $dealer->name }}
                                                        ({{ $dealer->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card search_report" style="margin-bottom: 0px;">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <div class="card show-class" style="margin-bottom: 0px;">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <table id="datatable" class="table table-bordered table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th width="10%" class="text-center">SL#</th>
                                                                    <th class="text-left" width="60%">Dealer</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr id="1">
                                                                    <td class="text-center"><span
                                                                            class="sl">1</span></td>
                                                                    <td class="text-left">
                                                                        <div class="form-group mb-0">
                                                                            <select class="form-control chosen-select"
                                                                                name="dealer_id[]" id="dealer_id" required>
                                                                                <option value="">Select Dealer</option>
                                                                                @foreach ($dealers as $dealer)
                                                                                    <option
                                                                                        value="{{ $dealer->id }}">
                                                                                        {{ $dealer->name }}
                                                                                        ({{ $dealer->code }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="addItem_1 btn btn-outline-info btn-sm"
                                                                            onclick="addItem()"><i
                                                                                class="fa fa-plus"></i></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <button type="submit"
                                                            class="btn btn-outline-info btn-lg waves-effect"><i
                                                                class="fa fa-spinner" aria-hidden="true"></i>
                                                            Process</button>
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
        <script>
            function addItem() {
                var last_row_id = parseInt($('#datatable tbody tr:last').attr('id'));
                var row_id = parseInt(last_row_id) + 1;
                if (isNaN(row_id)) {
                    var row_id = 1;
                }
                $('.addItem_' + last_row_id).hide();
                $("#datatable tbody").append(
                    `
                    <tr id="${row_id}">
                            <td class="text-center"><span
                                    class="sl">${row_id}</span></td>
                            <td class="text-left">
                                <div class="form-group mb-0">
                                    <select class="form-control chosen-select mb-0"
                                        name="dealer_id[]" id="dealer_id" required>
                                        <option value="">Select Dealer</option>
                                        @foreach ($dealers as $dealer)
                                            <option value="{{ $dealer->id }}">
                                                {{ $dealer->name }}
                                                ({{ $dealer->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <span class="btn btn-outline-danger btn-sm" onclick="remove(${row_id})"><i
                                        class="fa fa-plus"></i></span>
                                <span class="addItem_${row_id} btn btn-outline-info btn-sm" onclick="addItem()"><i
                                        class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                        `
                );

                $('.chosen-select').chosen();
                $('.chosen-select').trigger("chosen:updated");
            }


            function remove(id) {
                $('#' + id).remove();
                var last_row_id = parseInt($('#datatable tbody tr:last').attr('id'));

                $('.addItem_' + last_row_id).show();
            }
        </script>
    </body>

    </html>
