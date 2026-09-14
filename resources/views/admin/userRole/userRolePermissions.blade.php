@extends('admin.layouts.master')

@section('custom_css')
    <style>
        .tree ul ul {
            background: none;
        }

        input:-moz-read-only {
            opacity: .5;
        }

        input:read-only {
            opacity: .5;
        }

        .spinner-div {
            position: absolute;
            top: 47%;
            left: 38%;
            z-index: 99;
        }

    </style>
@endsection

@section('content')

    <div class="spinner-div">
        <img src="{{ asset('public/spinner2.gif') }}" class="spinner" alt="snipperImage">
    </div>

    <form class="form-horizontal" id="search" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="tree">
                            <ul id="makeTree">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
            padding: .5rem;
        }

        .card-header:not(.collapsed) .rotate-icon {
            transform: rotate(180deg);
        }

    </style>
@endsection

@section('custom-js')
    <script type="text/javascript">
        // load tree using ajax also try to show a loader
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $.ajax({
                type: "POST",
                url: "{{ route('userRole.loadMenu') }}",
                success: function(response) {
                    console.log(response);
                    $('#makeTree').html(response);
                    make_tree_menu('makeTree');
                    $('.spinner-div').hide();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        });
    </script>
@endsection
