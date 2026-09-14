@extends('admin.layouts.masterAddEditBlank')

@section('custom_css')
<style type="text/css">
    .table th{
        background: #00c292;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            <div class="col-md-6 text-right">
                <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                    <i class="fa fa-arrow-circle-left"></i> Go Back
                </a>

                <span class="btn btn-outline-info btn-lg" id="approveSuggest">
                    Click For Approve Suggest
                </span>
                <span class="btn btn-outline-info btn-lg" id="leaveSuggest">
                    Click For Leave Suggest
                </span>

            </div>
        </div>
    </div>
    <div class="card-body" id="LeaveRequestTable">
        <div class="table-responsive">

            <table id="dataTable" class="table table-bordered table-striped" name="LeaveRequestTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Employee Name</th>
                        <th width="200px">Leave From</th>
                        <th width="100px">Leave To</th>
                        <th width="20px">Duration</th>
                        <th width="20px" class="text-nowrap">Leave Type</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                    $sl = 1;
                    @endphp
                    @foreach ($leaverequests as $leaverequest)
                    <tr class="row_{{ $leaverequest->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $leaverequest->staffName }}</td>
                        <td>{{ $leaverequest->leave_from }}</td>
                        <td>{{ $leaverequest->leave_to }}</td>
                        <td>{{ $leaverequest->duration }}</td>
                        <td>{{ $leaverequest->leaveType }}</td>
                        <td align="center">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#LeaveRequestModal" onclick="showSuggDetails({{ $leaverequest->id }})">Approved <span class="badge badge-light"></span></button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="card-body" id="ApproveRequestTable">
        <div class="table-responsive">

            <table id="ApproveRequestTable_dataTable" class="table table-bordered table-striped" name="ApproveRequestTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Employee Name</th>
                        <th width="200px">Leave From</th>
                        <th width="100px">Leave To</th>
                        <th width="20px">Duration</th>
                        <th width="20px" class="text-nowrap">Leave Type</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                    $sl = 1;
                    @endphp
                    @foreach ($leaverequests_suggests as $leaverequests_suggest)
                    <tr class="row_{{ $leaverequests_suggest->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $leaverequests_suggest->staffName }}</td>
                        <td>{{ $leaverequests_suggest->leave_from }}</td>
                        <td>{{ $leaverequests_suggest->leave_to }}</td>
                        <td>{{ $leaverequests_suggest->duration }}</td>
                        <td>{{ $leaverequests_suggest->leaveType }}</td>
                        <td align="center">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ApproveModal" onclick="showApproveDetails({{ $leaverequests_suggest->id }})">Approved <span class="badge badge-light"></span></button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- The Modal --}}
    <div class="modal fade" id="LeaveRequestModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h3 class="modal-title dealerName" id="dealerName">Leave Request Suggest</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">

                    <form action="{{ route('leaveRequestSug.save') }}" method="post">
                        {{ csrf_field() }}
                        <table class="table table-bordered table-sm showDetails">
    <!--                        <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th width="200px">Leave From</th>
                                    <th width="100px">Leave To</th>
                                    <th width="20px">Duration</th>
                                    <th width="20px" class="text-nowrap">Leave Type</th>
                                </tr>
                            </thead>-->

                            <tbody id="tbody">

                            </tbody>
                        </table>
                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect">Approve</button>
                    <button type="button" class="btn btn-outline-danger btn-lg waves-effect" data-dismiss="modal">Close</button>
                </div>

                </form>
            </div>
        </div>
    </div>

    {{-- The Modal --}}
    <div class="modal fade" id="ApproveModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h3 class="modal-title dealerName" id="dealerName">Leave Request Approve</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">

                    <form action="{{ route('leaveRequestSug.update') }}" method="post">
                        {{ csrf_field() }}
                        <table class="table table-bordered table-sm showDetails">
    <!--                        <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th width="200px">Leave From</th>
                                    <th width="100px">Leave To</th>
                                    <th width="20px">Duration</th>
                                    <th width="20px" class="text-nowrap">Leave Type</th>
                                </tr>
                            </thead>-->

                            <tbody id="tbody">

                            </tbody>
                        </table>
                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect">Approve</button>
                    <button type="button" class="btn btn-outline-danger btn-lg waves-effect" data-dismiss="modal">Close</button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    function showSuggDetails(id) {
    $.ajax({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
            type: "POST",
            url: "{{ route('leaveRequestSug.leaveInfo') }}",
            data:{id:id},
            success: function(response) {
            var leaveInfo = response.leaveRequest;
            $('#LeaveRequestModal .showDetails tbody tr').remove();
            $("#LeaveRequestModal .showDetails tbody").append(
                    `
                        <tr>
                            <td colspan="2">
                            <input type="hidden" name="id" value="${leaveInfo.id}">
                            Employee Name: <input type="text" class="form-control" value="${leaveInfo.staffName}-(${leaveInfo.code})" readonly> 
                            </td>
                            <td colspan="2">Leave Type: <input type="text" class="form-control" value="${leaveInfo.leaveType}" readonly> </td>
                            <td>Available Leave: <input type="text" class="form-control available_leave" value="${response.dueDuration}" readonly> </td>
                        </tr>
                        <tr>
                            <td>Leave From: <input type="text" class="form-control" value="${leaveInfo.leave_from}" readonly> </td>
                            <td>Leave To: <input type="text" class="form-control" value="${leaveInfo.leave_to}" readonly> </td>
                            <td>Suggest Leave From: <input type="text" name="leave_from_a" class="form-control datepicker leave_from" value="${leaveInfo.leave_from}"> </td>
                            <td>Suggest To: <input type="text" name="leave_to_a" class="form-control datepicker leave_to" value="${leaveInfo.leave_to}"> </td>
                            <td>Leave Duration: <input type="text" name="leave_day" class="form-control leave_day" value="${leaveInfo.duration}" readonly> </td>
                        </tr>
                     </form>
                `
                    );
            },
            error: function(response) {

            }
    });
    }

    function showApproveDetails(id) {
    $.ajax({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
            type: "POST",
            url: "{{ route('leaveRequestSug.leaveInfo') }}",
            data:{id:id},
            success: function(response) {
            var leaveInfo = response.leaveRequest;
            $('#ApproveModal .showDetails tbody tr').remove();
            $("#ApproveModal .showDetails tbody").append(
                    `
                        <tr>
                            <td colspan="2">
                            <input type="hidden" name="id" value="${leaveInfo.id}">
                            Employee Name: <input type="text" class="form-control" value="${leaveInfo.staffName}-(${leaveInfo.code})" readonly> 
                            </td>
                            <td colspan="2">Leave Type: <input type="text" class="form-control" value="${leaveInfo.leaveType}" readonly> </td>
                            <td>Available Leave: <input type="text" class="form-control available_leave" value="${response.dueDuration}" readonly> </td>
                        </tr>
                        <tr>
                            <td>Leave From: <input type="text" name="leave_from" class="form-control" value="${leaveInfo.leave_from}" readonly> </td>
                            <td>Leave To: <input type="text" name="leave_to" class="form-control" value="${leaveInfo.leave_to}" readonly> </td>
                            <td>Suggest Leave From: <input type="text" name="leave_from_a" class="form-control datepicker leave_from" value="${leaveInfo.leave_from}"> </td>
                            <td>Suggest To: <input type="text" name="leave_to_a" class="form-control datepicker leave_to" value="${leaveInfo.leave_to}"> </td>
                            <td>Leave Duration: <input type="text" name="leave_day" class="form-control leave_day" value="${leaveInfo.duration}" readonly> </td>
                        </tr>
                     </form>
                `
                    );
            },
            error: function(response) {

            }
    });
    }


    $('#LeaveRequestModal').on('shown.bs.modal', function(e) {

    $(".datepicker").datepicker({
    format: 'dd-mm-yyyy',
            changeMonth: true,
            changeYear: true,
    });
    $(function() {
    $('[data-toggle="datepicker"]').datepicker({
    autoHide: true,
            zIndex: 2048,
    });
    })
            $('.datepicker').change(function () {
    var start = $(".leave_from").datepicker("getDate");
    var end = $(".leave_to").datepicker("getDate");
    days = (end - start) / (1000 * 60 * 60 * 24);
    $('.leave_day').val(Math.round(days + 1));
    });
    });
    $('#ApproveModal').on('shown.bs.modal', function(e) {

    $(".datepicker").datepicker({
    format: 'dd-mm-yyyy',
            changeMonth: true,
            changeYear: true,
    });
    $(function() {
    $('[data-toggle="datepicker"]').datepicker({
    autoHide: true,
            zIndex: 2048,
    });
    })
            $('.datepicker').change(function () {
    var start = $(".leave_from").datepicker("getDate");
    var end = $(".leave_to").datepicker("getDate");
    days = (end - start) / (1000 * 60 * 60 * 24);
    $('.leave_day').val(Math.round(days + 1));
    });
    });
//    $(".datepicker").datepicker({
//    format: 'dd-mm-yyyy',
//            changeMonth: true,
//            changeYear: true,
//            orientation: "bottom left"
//    });
//    $('.datepicker').change(function () {
//    var start = $(".leave_from").datepicker("getDate");
//    var end = $(".leave_to").datepicker("getDate");
//    days = (end - start) / (1000 * 60 * 60 * 24);
//    $('.leave_day').val(Math.round(days + 1));
//    });
    $(document).ready(function() {
    $('#ApproveRequestTable_dataTable').DataTable();
    $('#leaveSuggest').hide();
    $('#ApproveRequestTable').hide();
    });
//    function changeTable(){
    $('#approveSuggest').click(function() {
    $('#approveSuggest').hide();
    $('#LeaveRequestTable').hide();
    $('#ApproveRequestTable').show();
    $('#leaveSuggest').show();
    });
    $('#leaveSuggest').click(function() {
    $('#approveSuggest').show();
    $('#LeaveRequestTable').show();
    $('#ApproveRequestTable').hide();
    $('#leaveSuggest').hide();
    });
</script>
@endsection