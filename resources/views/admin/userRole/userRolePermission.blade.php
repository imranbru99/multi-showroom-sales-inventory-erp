@extends('admin.layouts.master')

@section('custom-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->

    <div class="spinner-div">
        <img src="{{ asset('public/spinner2.gif') }}" class="spinner" alt="snipperImage">
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                        <div class="col-md-6">
                            <span class="shortlink d-flex justify-content-end" style="margin-top: 0px !important">
                                <a style="margin-right: 0px; font-size: 16px;" class="btn btn-outline-info btn-lg mr-2"
                                    href="{{ route('user-roles.index') }}">
                                    <i class="fa fa-arrow-circle-left"></i> Go Back
                                </a>
                                <button type="submit" class="btn btn-outline-info btn-lg">
                                    <i class="fa fa-save"></i>
                                    Update
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $message = Session::get('msg');
                        if (isset($message)) {
                            echo "<div style='display:inline-block;width: auto;' class='alert alert-success'><strong>" . $message . '</strong></div>';
                        }
                        Session::forget('msg');
                    @endphp

                    <div id="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" action="{{ route('userRole.permissionUpdate') }}" method="POST"
                                enctype="multipart/form-data" id="editUser" name="editUser">
                                {{ csrf_field() }}

                                @if (count($errors) > 0)
                                    <div style="display:inline-block;width: auto;" class="alert alert-danger">
                                        {{ $errors->first() }}</div>
                                @endif

                                <div class="modal-body">
                                    <input type="hidden" name="userroleId" value="{{ $userRole->id }}">

                                    {{-- <div class="row">
                                        <div class="col-md-2 m-b-20 text-right">
                                            <button type="submit" class="btn btn-outline-info waves-effect">Update</button>
                                        </div>
                                    </div> --}}

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="userMenus">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 m-b-20 text-right">
                                            <button type="submit" class="btn btn-outline-info waves-effect btn-lg">
                                                <i class="fa fa-save"></i>
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .card-header .title {
            font-size: 12px;
            color: #fff;
            font-weight: bold;
        }

        .card-header .accicon {
            float: right;
            font-size: 12px;
            width: 1.2em;
            color: #fff;
        }

        .card-header {
            cursor: pointer;
            border-bottom: none;
            padding: 5PX;
        }

        .card {
            border: 1px solid #ddd;
        }

        .card-body {
            border-top: 1px solid #ddd;
            padding: 1rem;
        }

        .card-header:not(.collapsed) .rotate-icon {
            transform: rotate(180deg);
        }

    </style>
    {{-- @include('admin.partials.footer-assets') --}}
    <script src="{{ asset('/public/admin-elite/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                type: "POST",
                url: "{{ route('userRole.loadMenu') }}",
                data: {
                    role: "{{ $userRole->id }}"
                },
                success: function(response) {
                    // console.log(response);
                    $('#userMenus').html(response);
                    // make_tree_menu('makeTree');
                    $('.spinner-div').hide();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        });



        function selectAll() {
            if ($('.select_all').is(":checked")) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
            $('card-body').collapse('show');
        };


        function menu(id) {
            var menuId = id;
            if ($('#cd_' + id).is(":checked")) {
                $('.parentMenu_' + menuId).each(function() {
                    this.checked = true;
                });

                $('.childMenu_' + menuId).each(function() {
                    this.checked = true;
                });
            } else {
                $('.parentMenu_' + menuId).each(function() {
                    this.checked = false;
                });

                $('.childMenu_' + menuId).each(function() {
                    this.checked = false;
                });
            }

            $('.coll_' + id).css('display', 'block');
        };
    </script>

@endsection
