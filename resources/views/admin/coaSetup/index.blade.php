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

        .tree ul ul {
            background-color: none !important;
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
                    <div class="col-md-6">
                        <div class="tree">
                            <ul id="makeTree">
                                {{-- @php
			                        $visit = array();
			                        $i = 0;
			                        foreach ($coaLists as $coaList)
			                        {
			                            $visit[$i] = false;
			                            $i++;
			                        }
			                        echo \App\HelperClass::makeTree("COA","0",$coaLists,$visit,0);
		                    	@endphp --}}
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" class="form-control" id="presentGeneralLedger"
                                    name="presentGeneralLedger" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" class="form-control" id="currentGeneralLedger"
                                    name="currentGeneralLedger" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('headCode') ? ' has-danger' : '' }}"
                                    style="margin: 2px;">
                                    <label for="head-code">Head Code</label>
                                    <input type="text" class="form-control" id="headCode" name="headCode"
                                        value="{{ old('headCode') }}" required>
                                    @if ($errors->has('headCode'))
                                        @foreach ($errors->get('headCode') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('parentHead') ? ' has-danger' : '' }}"
                                    style="margin: 2px;">
                                    <label for="parent-head">Parent Head</label>
                                    <input type="text" class="form-control" id="parentHead" name="parentHead"
                                        value="{{ old('parentHead') }}" required>
                                    @if ($errors->has('parentHead'))
                                        @foreach ($errors->get('parentHead') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('headName') ? ' has-danger' : '' }}"
                                    style="margin: 2px;">
                                    <label for="head-name">Head Name</label>
                                    <input type="text" class="form-control" id="headName" name="headName"
                                        value="{{ old('headName') }}" required>
				    <input type="hidden" class="form-control" id="headId" name="headId"
                                        value="{{ old('headId') }}">
                                    @if ($errors->has('headName'))
                                        @foreach ($errors->get('headName') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('headLevel') ? ' has-danger' : '' }}"
                                    style="margin: 2px;">
                                    <label for="head-level">Head Level</label>
                                    <input type="text" class="form-control" id="headLevel" name="headLevel"
                                        value="{{ old('headLevel') }}" required>
                                    @if ($errors->has('headLevel'))
                                        @foreach ($errors->get('headLevel') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('headType') ? ' has-danger' : '' }}"
                                    style="margin: 2px;">
                                    <label for="head-type">Head Type</label>
                                    <input type="text" class="form-control" id="headType" name="headType"
                                        value="{{ old('headType') }}" required>
                                    @if ($errors->has('headType'))
                                        @foreach ($errors->get('headType') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}"
                                    style="height: 40px; line-height: 40px; margin: 2px">
                                    <div class="form-check-inline">
                                        <label class="form-check-label" for="">
                                            <input type="checkbox" class="form-check-input" id="active" name="active"
                                                value="1" checked>Active
                                        </label>
                                    </div>

                                    <div class="form-check-inline">
                                        <label class="form-check-label" for="">
                                            <input type="checkbox" class="form-check-input" id="transaction"
                                                name="transaction" onchange="transactionChange()" value="1">Transaction
                                        </label>
                                    </div>

                                    <div class="form-check-inline">
                                        <label class="form-check-label" for="">
                                            <input type="checkbox" class="form-check-input" id="generalLedger"
                                                name="generalLedger" onchange="generalLedgerChange()" value="1">General
                                            Ledger
                                        </label>
                                    </div>
                                    @if ($errors->has('type'))
                                        @foreach ($errors->get('type') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}"
                                    style="height: 40px; line-height: 40px;">
                                    <button type="button" class="btn btn-outline-info btn-md waves-effect" id="btnNew"
                                        name="btnNew" value="new" disabled="disabled" onclick="btnNewClick()"><i
                                            class="fa fa-plus"></i> New</button>
                                    <button type="submit" class="btn btn-outline-info btn-md waves-effect" id="btnAction"
                                        name="btnAction" value="save" disabled="disabled"><i class="fa fa-plus"></i>
                                        Save</button>
                                    <button type="reset" class="btn btn-outline-info btn-md waves-effect" id="btnClear"
                                        name="btnClear" value="clear" onclick="btnClearClick()"><i class="fa fa-eraser"></i>
                                        Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .tree ul ul {
            background-color: none !important;
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
                url: "{{ route('coaSetup.loadCoaTree') }}",
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


        // make_tree_menu('makeTree');


        function btnClearClick() {
            $("#btnAction").html('<i class="fa fa-plus"></i> Save');
            $("#btnAction").val('save');
            $("#btnAction").prop("disabled", true);
            $("#btnNew").prop("disabled", true);
        }

        function btnNewClick() {
            var headCode = $('#headCode').val();
            $("#headName").focus();
            if (headCode !== "") {
                if ($("#generalLedger").prop('checked') === true) {

                    if ($("#transaction").prop('checked') != true) {
                        ajaxGetdata(headCode);
                    } else {
                        swal("Can't Make New Head", "", "warning");
                        return false;
                    }
                    $("#generalLedger").attr("readonly", true);

                    // swal("Can't Make New GL Head.But Create New Transection","","warning");
                    return false;

                } else {
                    ajaxGetdata(headCode);
                    return true;
                }
                return false;
            } else {
                swal("Please Select A Head", "", "warning");
            }
        }

        function ajaxGetdata(headCode) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                url: "{{ route('coaSetup.newCoaData') }}",
                dataType: "json",
                data: {
                    headCode: headCode
                },
                success: function(data) {
                    if (data) {
                        $('#headCode').val(data.headCode);
                        $('#parentHead').val(data.parentHead);
                        $("#headName").val('');
                        $('#headLevel').val(data.headLevel);
                        $('#headType').val(data.headType);

                        $("#btnNew").prop("disabled", true);
                        $("#btnAction").prop("disabled", false);

                        $("#headName").prop('required', true);
                        $('#transaction').prop('disabled', false);
                        $('#generalLedger').prop('disabled', false);

                        if (data.active) {
                            $("#active").prop("checked", true);
                        } else {
                            $("#active").prop("checked", false);
                        }

                        if (data.generalLedger) {
                            $("#generalLedger").prop("checked", false);
                        } else {
                            $("#generalLedger").prop("checked", true);
                        }

                        if (data.transaction) {
                            $("#transaction").prop("checked", true);
                            $("#generalLedger").prop("checked", false);
                            $("#generalLedger").attr("readonly", true);
                        } else {
                            $("#transaction").prop("checked", false);
                            // $('#generalLedger').attr('checked', 'checked');
                            // $("#generalLedger").attr("readonly", true);
                            // $('#IsGL').css("cursor","pointer");
                        }
                        $("#btnAction").val('save');
                        $("#btnAction").html('<i class="fa fa-plus"></i> Save');
                    } else {
                        // $("#btnAction").val('save');
                        $("#btnNew").prop("disabled", true);
                        $("#btnAction").prop("disabled", true);
                        $('#headCode').val('');
                        $('#headLevel').val('');
                        $('#headType').val('');
                        $('#parentHead').val('');
                        $("#headName").prop('required', false);
                        $('#transaction').prop('disabled', false);
                        $('#generalLedger').prop('disabled', false);
                        $("#generalLedger").prop("checked", false);
                        $("#transaction").prop("checked", false);
                    }
                }
            });
        }

        function transactionChange() {
            if ($("#transaction").prop('checked') == true) {
                if ($("#presentGeneralLedger").val() == 0) {
                    if ($("#currentGeneralLedger").val() == 0) {
                        swal("This Previous Head Is Not A GL Head Show This Head Cann't Be A Transaction Head", "",
                            "warning");
                        $("#transaction").prop("checked", false);
                    }
                }
            }
        }

        function generalLedgerChange() {
            if ($("#generalLedger").prop('checked') === true) {

                // console.log($("#presentGeneralLedger").val() == 1);

                if ($("#presentGeneralLedger").val() == 1) {
                    swal("Previous Head Is A GL Head\nSo This Head Cann't Be A GL Head", "", "warning");
                    $("#generalLedger").prop("checked", false);
                } else {
                    $("#currentGeneralLedger").val('1');
                }
            } else {
                $("#currentGeneralLedger").val('0');
                //            $("#IsTransaction").prop("checked", false);
            }
        }

        function loadData(headCode) {
            // $("#headName").focus();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ route('coaSetup.loadCoaData') }}",
                dataType: "json",
                data: {
                    headCode: headCode
                },
                success: function(data) {
                    $("#headName").focus();
                    if (data) {
                        $('#currentGeneralLedger').val(data.currentGeneralLedger);
                        $('#presentGeneralLedger').val(data.presentGeneralLedger);
			$('#headId').val(data.id);
                        $('#headCode').val(data.headCode);
                        $('#headLevel').val(data.headLevel);
                        $('#headName').val(data.headName);
                        $('#headType').val(data.headType);
                        $('#parentHead').val(data.parentHead);

                        $("#btnAction").html('<i class="fa fa-pencil-square-o"></i> Update');
                        $("#btnAction").val('update');
                        $("#btnNew").prop("disabled", false);
                        $("#transaction").prop("checked", false);
                        $("#active").prop("checked", false);
                        $("#generalLedger").prop("checked", false);
                        $("#headName").prop('required', true);

                        if (data.headLevel == 0) {
                            $("#btnAction").prop("disabled", true);
                        } else {
                            $("#btnAction").prop("disabled", false);
                        }
                        // console.log(data.SubGlHead,'data.SubGlHeaddata.SubGlHead');

                        if (data.active == 1) {
                            $("#active").prop("checked", true);
                        } else {
                            $("#active").prop("checked", false);
                        }

                        if (data.generalLedger == 1) {
                            $("#generalLedger").prop("checked", true);
                        }
                        // console.log(data.transaction,'data.transaction');

                        if (data.transaction == 1) {
                            $("#btnNew").attr("disabled", "disabled");
                            $("#transaction").prop("checked", true);
                            // $("#transaction").attr("readonly", true);
                            $("#generalLedger").attr("readonly", true);
                        }

                        if (data.currentGeneralLedger === 1) {
                            $("#generalLedger").prop("checked", true);
                            $("#generalLedger").attr("readonly", true);
                            // $("#transaction").attr("readonly", true);
                        }

                        if (data.currentGeneralLedger === 0) {
                            $("#generalLedger").attr("readonly", false);
                            $("#transaction").attr("readonly", false);
                        }

                    } else {
                        $("#btnNew").prop("disabled", true);
                        $("#btnAction").prop("disabled", true);

                        $("#btnAction").val('save');

                        $("#headName").prop('required', false);

                        $('#currentGeneralLedger').val('0');
                        $('#presentGeneralLedger').val('0');
                        $('#headCode').val('');
                        $('#headLevel').val('');
                        $('#headName').val('');
                        $('#headType').val('');
                        $('#parentHead').val('');

                        $("#generalLedger").prop("checked", true);
                        $("#transaction").prop("checked", false);
                    }
                }
            });
        }
        $("#headName").focus();

        (function($) {
            "use strict";
            $(document.body).delegate('[type="checkbox"][readonly="readonly"]', 'click', function(e) {
                e.preventDefault();
            });
        }(window.jQuery));

    </script>
@endsection
