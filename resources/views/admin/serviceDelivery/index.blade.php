@extends('admin.layouts.masterIndex')

@section('card_body')
<div class="card-body">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped"  name="serviceDelivery">
            <thead>
                <tr>
                    <th width="20px">SL</th>
                    <th>Delivery Issue To</th>
                    <th>Delivery Date</th>
                    <th width="100px">Product Receive Inv No</th>
                    <th>Job No</th>
                    <th>Product Name</th>
                    <th>Product Model</th>
                    <th>Product Serial</th>
                    <th>Dealer</th>
                    <th>Delivery Condition</th>  
                    <th>Payment Type</th>
                    <th>Service Amount</th>
                    <th width="20px">Action</th>
                </tr>
            </thead>
            <tbody id="">
                @foreach ($serviceDeliveries as $serviceDelivery)
                <tr class="row_{{ $serviceDelivery->id }}">
                    <td></td>
                    <td>{{ date('d-m-Y', strtotime($serviceDelivery->delivery_issue_to)) }}</td>
                    <td>{{ date('d-m-Y', strtotime($serviceDelivery->delivery_date)) }}</td>
                    <td>{{ $serviceDelivery->serviceAllocation->serviceProduct->invoice_no }}</td>
                    <td>{{ $serviceDelivery->invoice_no }}</td>
                    <td>{{ @$serviceDelivery->serviceAllocation->product->name }}</td>
                    <td>{{ @$serviceDelivery->serviceAllocation->product_model }}</td>
                    <td>{{ @$serviceDelivery->serviceAllocation->product_serial }}</td>
                    <td>{{ @$serviceDelivery->serviceAllocation->serviceProduct->dealer->name }}</td>
                    <td>{{ $serviceDelivery->delivery_condition }}</td>
                    <td class="text-capitalize">{{ $serviceDelivery->service_payment_type }}</td>
                    <td>{{ $serviceDelivery->service_amount }}</td>
                    <td class="text-nowrap">
                        <?php echo \App\Link::action($serviceDelivery->id) ?>
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

            deliveryId = $(this).parent().data('id');
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
                                url: "{{ route('serviceDelivery.delete') }}",
                                data: {deliveryId: deliveryId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Area Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + deliveryId).remove();
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