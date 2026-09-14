@extends('admin.layouts.masterAddEdit')

@section('custom_css')
<style type="text/css">
    #total-target {
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="driver_name">Root Plan No</label>
                <input type="number" name="root_plan_no" class="form-control" value="{{ $planNo  }}" placeholder="Root Plan Number" readonly>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="driver_name">Driver Name</label>
                <input type="text" name="driver_name" class="form-control" value="" placeholder="Driver Name" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="contact_no">Contact No</label>
                <input type="text" name="contact_no" class="form-control" value="" placeholder="Contact No" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" name="date" class="form-control datepicker" value="{{ date('d-m-Y') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="vehicle_no">Vehicle No</label>
                <select class="form-control chosen-select" name="vehicle_id" id="vehicle">
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">{{$vehicle->registration_no}} - ({{ $vehicle->type }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vehicle_capacity">Vehicle Capacity</label>
                        <input type="text" name="vehicle_capacity" class="form-control" value="0" placeholder="Vehicle Capacity" id="vehicle_capacity" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vehicle_capacity">Load Capacity</label>
                        <input type="text" name="product_capacity" class="form-control" value="0" placeholder="Product Capacity" id="product_capacity" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="driver_name">Root Plan</label>
                <select class="form-control chosen-select" name="root_plan[]" multiple id="root_plan">
                    @foreach($territories as $territory)
                    <option value="{{ $territory->id }}">{{ $territory->name }} > {{ @$territory->area->name }} > {{ @$territory->area->region->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-md-12">
            <button type="button" class="btn btn-outline-info float-right btn-lg" onclick="Load()"><span class="fa fa-refresh"></span> Load</button>
        </div>
    </div>


    <table id="invoices" class="table table-bordered table-striped mt-5">
        <thead>
            <tr class="bg-success">
                <th>Dealer Name</th>
                <th>Product Name</th>
                <th>Model No</th>
                <th>Serial No</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection

@section('custom-js')
<script>
    function Load() {
        var roads = $('#root_plan').val();
        $('#product_capacity').val(0);

        if (roads == '') {
            swal({
                title: "<small class='text-danger'>Error!</small>"
                , type: "warning"
                , text: "Select Root Plan!"
                , html: true
            , });
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "get"
            , url: "{{ route('rootPlan.getInvoices') }}"
            , data: {
                roads: roads
                , id: ''
            },

            success: function(response) {
                $('#invoices tbody tr').remove();
                if (response.length > 0) {
                    response.forEach(function(item, index) {

                        item.products.forEach(function(ab, bc) {
                            var tr = `
                                <tr>
                                    <td>${item.dealer.name}</td>
                                    <td>${ab.product.name}</td>
                                    <td>${ab.model_no}</td>
                                    <td>${ab.serial_no}</td>
                                    <td><input type = "checkbox" id="checked_${ab.id}" name = "issue_list_id[]" value ="${ab.id}" onclick="addRemove(${ab.id})"> </td>
                                </tr>
                            `;

                            $('#invoices tbody').append(tr);
                        });
                    });
                }

            }
            , error: function(response) {

            }
        });
    }

    $('#vehicle').change(function() {
        var id = $(this).val();

        $.ajax({
            type: "get"
            , url: "{{ route('rootPlan.getVehicle') }}"
            , data: {
                id: id
            },

            success: function(response) {
                $('#vehicle_capacity').val(response.capacity);
            }
        });
    });

    function addRemove(id) {
        $.ajax({
            type: "get"
            , url: "{{ route('rootPlan.getProductInfo') }}"
            , data: {
                id: id
            },

            success: function(response) {
                var checked = $('#checked_' + id).is(":checked");

                var vehicleCapacity = parseInt($('#vehicle_capacity').val());
                var productCapacity = parseInt($('#product_capacity').val());
                var totalCapacity = productCapacity + parseInt(response);
                if (checked == true) {
                    if(totalCapacity > vehicleCapacity){
                        $('#checked_' + id).filter(':checkbox').prop('checked',false);
                        swal("Capacity is greater than Vehicle Capacity!", "", "warning");
                        return;
                    }
                    $('#product_capacity').val(totalCapacity);
                }
                if (checked != true) {
                    $('#product_capacity').val(productCapacity - parseInt(response));
                }
            }
        });
    }

</script>
@endsection
