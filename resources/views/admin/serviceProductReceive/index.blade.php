@extends('admin.layouts.masterIndex')

@section('card_body')
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
                @foreach ($serviceProductReceives as $serviceProductReceive)
                <tr class="row_{{ $serviceProductReceive->id }}">
                    <td></td>
                    <td>{{ $serviceProductReceive->receive_date }}</td>
                    <td>{{ $serviceProductReceive->invoice_no }}</td>
                    <td>{{ $serviceProductReceive->product->name }}</td>
                    <td>{{ $serviceProductReceive->product_model }}</td>
                    <td>{{ $serviceProductReceive->product_serial }}</td>
                    <td>{{ @$serviceProductReceive->dealer->name }} ({{ @$serviceProductReceive->dealer->code }})</td>
                    <td>{{ $serviceProductReceive->problem_list }}</td>
                    <td>{{ $serviceProductReceive->product_condition }}</td>
                    <td>{{ $serviceProductReceive->qty }}</td>
                    <td>
                        @php
                        echo \App\Link::action($serviceProductReceive->id);
                        @endphp                				
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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