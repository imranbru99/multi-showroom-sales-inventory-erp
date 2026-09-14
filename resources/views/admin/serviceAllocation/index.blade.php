@extends('admin.layouts.masterAddEditBlank')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped"  name="serviceProductReceive">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Receive Date</th>
                        <th>Invoice No</th>
                        <th>Product Name</th>
                        <th>Product Model</th>
                        <th>Product Serial</th>
                        <th>Dealer</th>
                        <th>Problem List</th>
                        <th>Product Condition</th>                    
                        <th>Qty</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @foreach ($serviceAllocation as $serviceAllocate)
                    <?php
                    $models = explode(',', $serviceAllocate->product_model);
                    if (!empty($serviceAllocate->product_serial)) {
                        $serials = explode(',', $serviceAllocate->product_serial);
                    } else {
                        $serials = [];
                    }
//                dd($serials);
                    foreach ($models as $key => $model) {
                        $model = str_replace(' ', '', $model);
                        $assigned = \App\ServiceAllocation::where('service_receive_id', $serviceAllocate->id)
                                ->where('product_model', $model)
                                ->first();

                        if (empty($assigned)) {
                            ?>
                            <tr class="row_{{ $serviceAllocate->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($serviceAllocate->receive_date)) }}</td>
                                <td>{{ $serviceAllocate->invoice_no }}</td>
                                <td>{{ $serviceAllocate->product->name }}</td>
                                <td>{{ $model }}</td>
                                <td>{{ @$serials[$key] }}</td>
                                <td>{{ @$serviceAllocate->dealer->name }} ({{ @$serviceAllocate->dealer->code }})</td>
                                <td>{{ $serviceAllocate->problem_list }}</td>
                                <td>{{ $serviceAllocate->product_condition }}</td>
                                <td>1</td>
                                <td>
                                    <button class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#assignModal_{{ $model }}">Assign</button>               				
                                </td>
                            </tr>

                        <div class="modal fade" id="assignModal_{{ $model }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Assign Service Engineer</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('serviceAllocation.save') }}" method="post"> 
                                        {{ csrf_field() }}
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Job No</label>
                                                    <input type="hidden" class="form-control" name="service_receive_id" value="{{ $serviceAllocate->id }}">
                                                    <input type="text" class="form-control" name="invoice_no" value="{{ $invoiceNo }}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="">Issue Date</label>
                                                    <input type="text" class="form-control datepicker" name="issue_date" value="{{ date('d-m-Y') }}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Product Name</label>
                                                        <input type="hidden" class="form-control" name="product_id" value="{{ $serviceAllocate->product_id }}">
                                                        <input type="text" class="form-control" name="product_name" value="{{ $serviceAllocate->product->name }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="">Product Model</label>
                                                    <input type="text" class="form-control" name="product_model" value="{{ $model }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="">Product Serial</label>
                                                    <input type="text" class="form-control" name="product_serial" value="{{ @$serials[$key] }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="">Qty</label>
                                                    <input type="text" class="form-control" name="qty" value="1" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="">Select Engineer</label>
                                                    <select class="form-control chosen-select" name="employee_id">
                                                        @foreach($staffs as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->code }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="">Remarks</label>
                                                    <textarea class="form-control" rows="5" name="remarks"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-outline-info">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function () {
        var updateThis;

        //ajax delete code
        $('#dataTable tbody').on('click', 'i.fa-trash', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            serviceId = $(this).parent().data('id');
            var tableRow = this;
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this information!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('serviceProductReceive.delete') }}",
                                data: {serviceId: serviceId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Area Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + serviceId).remove();
                                },
                                error: function (response) {
                                    error = "Failed.";
                                    swal({
                                        title: "<small class='text-danger'>Error!</small>",
                                        type: "error",
                                        text: error,
                                        timer: 1000,
                                        html: true,
                                    });
                                }
                            });
                        } else
                        {
                            swal({
                                title: "Cancelled",
                                type: "error",
                                text: "This Area Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });
</script>
@endsection