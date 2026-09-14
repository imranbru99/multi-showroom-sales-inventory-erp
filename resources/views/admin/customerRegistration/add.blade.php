@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        .blockTitle {
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }

    </style>
@endsection

@section('card_body')
    @include('admin.customerRegistration.add_form')
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".spouceName").prop('disabled', true);
            $('.maritalStatus').click(function(event) {
                var maritalStatus = $('.maritalStatus').val();
                if (maritalStatus == "Married") {
                    $(".spouceName").prop('disabled', false);
                } else {
                    $(".spouceName").prop('disabled', true);
                }
            })
        });
    </script>

    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$project_id }}');

        $('.select_project').change(function() {
            var selected = $('.select_project').val();
            $('.go_back').attr('href', href + '?project=' + selected);
        });
    </script>
@endsection
