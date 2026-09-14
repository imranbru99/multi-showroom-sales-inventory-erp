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
                        <th>Issue Date</th>
                        <th>Job No</th>
                        <th>Product Name</th>
                        <th>Product Model</th>
                        <th>Product Serial</th>
                        <th>Customer Name</th>
                        <th>Engineer</th>
                        <th>Problem List</th>
                        <th>Product Condition</th>                    
                        <th>Qty</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @foreach ($initials as $initial)

                    <tr class="row_{{ $initial->id }}">
                        <td></td>
                        <td>{{ date('d-m-Y', strtotime($initial->issue_date)) }}</td>
                        <td>{{ $initial->invoice_no }}</td>
                        <td>{{ $initial->product->name }}</td>
                        <td>{{ $initial->product_model }}</td>
                        <td>{{ $initial->product_serial }}</td>
                        <td>{{ @$initial->serviceProduct->dealer->name }} ({{ @$initial->serviceProduct->dealer->code }})</td>
                        <td>{{ $initial->staff->name }} ({{ $initial->staff->code }})</td>
                        <td>{{ $initial->serviceProduct->problem_list }}</td>
                        <td>{{ $initial->serviceProduct->product_condition }}</td>
                        <td>1</td>
                        <td>
                            <button class="btn btn-outline-info btn-sm" onclick="viewModal({{ $initial->id }})">Change Status</button>               				
                            <input type="hidden" id="id_{{ $initial->id }}" name="id" value="{{ $initial->id }}">
                            <input type="hidden" id="invoice_no_{{ $initial->id }}" value="{{ $initial->invoice_no }}">
                            <input type="hidden" id="issue_date_{{ $initial->id }}" value="{{ date('d-m-Y', strtotime($initial->issue_date)) }}">
                            <input type="hidden" id="product_id_{{ $initial->id }}" value="{{ $initial->product_id }}">
                            <input type="hidden" id="product_name_{{ $initial->id }}" value="{{ $initial->product->name }}">
                            <input type="hidden" id="product_model_{{ $initial->id }}" value="{{ $initial->product_model }}">
                            <input type="hidden" id="product_serial_{{ $initial->id }}" value="{{ $initial->product_serial }}">
                            <input type="hidden" id="staff_{{ $initial->id }}" value="{{ @$initial->staff->name }} ({{ @$initial->staff->code }})">
                            @if(!empty($initial->remarks))
                            @foreach($initial->remarks as $remarks)
                            <input type="hidden" class="remarks_{{ $initial->id }}" value="{{ $remarks->remarks }}">
                            @endforeach
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--modal-->
    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign Service Engineer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('serviceAllocation.runningAllocationSave') }}" method="post" enctype="multipart/form-data"> 
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Job No</label>
                                <input type="hidden" class="form-control" id="modal_id" name="id" value="">
                                <input type="text" class="form-control" id="modal_invoice_no" name="invoice_no" value="">
                            </div>
                            <div class="col-md-4">
                                <label for="">Issue Date</label>
                                <input type="text" class="form-control" id="modal_issue_date" name="issue_date" value="" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="">Finish Date</label>
                                <input type="text" class="form-control datepicker" name="finish_date" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Product Name</label>
                                    <input type="hidden" class="form-control" id="modal_product_id" name="product_id" value="">
                                    <input type="text" class="form-control" id="modal_product_name" name="product_name" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="">Product Model</label>
                                <input type="text" class="form-control" id="modal_product_model" name="product_model" value="" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="">Product Serial</label>
                                <input type="text" class="form-control" id="modal_product_serial" name="product_serial" value="" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="">Qty</label>
                                <input type="text" class="form-control" name="qty" value="1" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="">Engineer</label>
                                <input type="text" class="form-control" id="modal_staff" value="" readonly>
                            </div>
                            <div class="col-md-12 spare_parts">
                                <label for="">Spare Parts</label>
                                <div class="col-md-12 d-flex" id="spare_parts_1">
                                    <div class="col-md-4">
                                        <select class="form-control chosen-select product_id_select" name="spare_products[]">
                                            <option value=''>Select Parts</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->model_no }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control chosen-select serial_no_select" name="spare_serial_no[]">
                                            <option value=''>Select Serial No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control spare_price" value="" name="spare_price[]">
                                    </div>
                                    <div class="col-md-2 product_btn_1">
                                        <span class="btn btn-outline-info mb-3 add_product_btn_1" onclick="addSpare(1)">Add More</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="">Remarks</label>
                                <div class="modal_remarks">
                                    <textarea class="form-control" rows="5" name="remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-info">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
    function viewModal(id) {
    var id = id;
    var invoice_no = $('#invoice_no_' + id).val();
    var issue_date = $('#issue_date_' + id).val();
    var product_id = $('#product_id_' + id).val();
    var product_name = $('#product_name_' + id).val();
    var product_model = $('#product_model_' + id).val();
    var product_serial = $('#product_serial_' + id).val();
    var staff = $('#staff_' + id).val();
//    var remarks = $('#remarks_' + id).val();
    $('.remarks_' + id).each(function() {
    $('.modal_remarks').prepend('<textarea class="form-control" rows="3" readonly>' + $(this).val() + '</textarea>');
    });
    $('#modal_id').val(id);
    $('#modal_invoice_no').val(invoice_no);
    $('#modal_issue_date').val(issue_date);
    $('#modal_product_id').val(product_id);
    $('#modal_product_name').val(product_name);
    $('#modal_product_model').val(product_model);
    $('#modal_product_serial').val(product_serial);
    $('#modal_staff').val(staff);
    $('#assignModal').modal('show');
    }

    function addSpare(i) {
    var row = i;
    var inc_row = i + 1;
    $('.add_product_btn_' + i).remove();
    $('.product_btn_' + i).html(
            `
                <span class="btn btn-outline-danger mb-3 add_product_btn_${i}" onclick="removeProduct(${i})">Remove</span>
                `
            );
    $('.spare_parts').append(
            `
                <div class="col-md-12 d-flex" id="spare_parts_${inc_row}">
                    <div class="col-md-4">
                        <select class="form-control chosen-select product_id_select" name="spare_products[]" required>
                            <option value=''>Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->model_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control chosen-select serial_no_select" name="spare_serial_no[]" required>
                            <option value=''>Select Serial No</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control spare_price" value="" name="spare_price[]" required>
                    </div>
                    <div class="col-md-2 product_btn_${inc_row}">
                        <span class="btn btn-outline-info mb-3 add_product_btn_${inc_row}" onclick="addSpare(${inc_row})">Add More</span>
                    </div>
                </div>
                `
            );
    $('.chosen-select').chosen();
    $('.chosen-select').trigger("chosen:updated");
    }

    function removeProduct(i) {
    $('#spare_parts_' + i).remove();
    }

    $(document).on('change', '.product_id_select', function () {
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    let parent = $(this).parent().parent();
    var product_id = parent.find('.product_id_select option:selected').val();
    if (product_id != '') {
    $.ajax({
    type: 'post',
            url: '{{ route('serviceAllocation.getSerialNo') }}',
            data: {
            product_id: product_id
            },
            success: function (data) {
            var serial_nos = data.serial_nos;
            parent.find('.serial_no_select option').remove();
            parent.find('.serial_no_select').append('<option value="">Select Serial No</option>');
            if (data.serial_nos.length > 0) {
            data.serial_nos.forEach(function (item, index) {
            var option = '<option value="' + item.serial_no +
                    '">' + item.serial_no + '</option>';
            parent.find('.serial_no_select').append(option);
            });
            $('.chosen-select').chosen();
            $('.chosen-select').trigger("chosen:updated");
            }


            }
    });
    }
    });
</script>
@endsection