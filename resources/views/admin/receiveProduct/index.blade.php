@extends('admin.layouts.master')

@section('content')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6 text-right">
                
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @php
            $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped" name="transferTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Transfer No.</th>
                        <th>Transfer Date.</th>
                        <th>Source Store</th>
                        <th>Destination Store</th>
                        <th>Qty</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                    $sl = 0;
                    @endphp
                    @foreach ($receives as $receive)
                    @php
                    $host = DB::table('tbl_stores')
                    ->select('name as hostName')
                    ->where('id', $receive->host_id)
                    ->first();
                    $destination = DB::table('tbl_stores')
                    ->select('name as destinationName')
                    ->where('id', $receive->destination_id)
                    ->first();
                    
                    @endphp
                    <tr class="row_{{ $receive->id }}">
                        <td>{{ $sl++ }}</td>
                        <td>{{ $receive->transfer_no }}</td>
                        <td>{{ date('d-m-Y', strtotime($receive->date)) }}</td>
                        <td>{{ @$host->hostName }}</td>
                        <td>{{ @$destination->destinationName }}</td>
                        <td>{{ $receive->total_qty }}</td>
                        <td class="d-flex">
                            <a href="{{ route('receiveProduct.print', $receive->id) }}" target="_blank"><button class="btn btn-outline-info btn-sm mr-1"><i class="fa fa-eye"></i>View</button> </a>
                            @if($receive->approve_by)
                            <button class="btn btn-outline-success btn-sm"> Approved</button>
                            @else
                            <a href="{{ route('receiveProduct.approve', $receive->id) }}"><button class="btn btn-outline-info btn-sm"><i class="fa fa-thumbs-o-up"></i>
                                    Approve</button></a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function() {
        var updateThis;

        //ajax delete code
        $('#dataTable tbody').on('click', 'i.fa-trash', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            transferId = $(this).parent().data('id');
            // console.log(liftingId);
            var tableRow = this;
            swal({
                    title: "Are you sure?"
                    , text: "You will not be able to recover this information!"
                    , type: "warning"
                    , showCancelButton: true
                    , confirmButtonColor: "#DD6B55"
                    , confirmButtonText: "Yes, delete it!"
                    , cancelButtonText: "No, cancel plx!"
                    , closeOnConfirm: false
                    , closeOnCancel: false
                }
                , function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: "POST"
                            , url: "{{ route('transferProduct.delete') }}"
                            , data: {
                                transferId: transferId
                            },

                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>"
                                    , type: "success"
                                    , text: "Transfer Deleted Successfully!"
                                    , timer: 1000
                                    , html: true
                                , });
                                $('.row_' + transferId).remove();
                            }
                            , error: function(response) {
                                error = "Failed.";
                                swal({
                                    title: "<small class='text-danger'>Error!</small>"
                                    , type: "error"
                                    , text: error
                                    , timer: 1000
                                    , html: true
                                , });
                            }
                        });
                    } else {
                        swal({
                            title: "Cancelled"
                            , type: "error"
                            , text: "This Transfer Is Safe :)"
                            , timer: 1000
                            , html: true
                        , });
                    }
                });
        });
    });

</script>
@endsection
