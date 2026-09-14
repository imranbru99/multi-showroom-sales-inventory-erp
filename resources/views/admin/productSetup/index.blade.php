@extends('admin.layouts.masterIndex')

@section('card_body')
<div class="card-body">
    <div class="table-responsive">
        @php
        $sl = 0;
        @endphp

        <table id="dataTable" class="table table-bordered table-striped"  name="productsTable">
            <thead>
                <tr>
                    <th width="20px">SL</th>
                    <th>Company Name</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Model</th>
                    <th>Lifting Price</th>
                    <th>Retail Price</th>
                    <th width="20px">Status</th>
                    <th width="20px">Action</th>
                </tr>
            </thead>
            <tbody id="tbody">
                @foreach($products as $product)                        	
                <tr class="row_{{ $product->id }}">
                    @php
                    $sl++;
                    @endphp
                    <td>{{ $sl }}</td>
                    <td>{{ $product->companyName }}</td>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->catName }}</td>
                    <td>{{ $product->model_no }}</td>
                    <td class="text-right">{{ number_format($product->price, 2, '.', '') }}</td>
                    <td class="text-right">{{ number_format($product->mrp_price, 2, '.', '') }}</td>                                       
                    <td><?php echo \App\Link::status($product->id, $product->status) ?></td>
                    <td class="text-nowrap"><?php echo \App\Link::action($product->id) ?></td>
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

            productId = $(this).parent().data('id');
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
                                url: "{{ route('productSetup.delete') }}",
                                data: {productId: productId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Product Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + productId).remove();
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
                                text: "Your Product Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });

    //ajax status change code
    function statusChange(productId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('productSetup.status') }}",
            data: {productId: productId},
            success: function (response) {
                swal({
                    title: "<small class='text-success'>Success!</small>",
                    type: "success",
                    text: "Status Successfully Updated!",
                    timer: 1000,
                    html: true,
                });
            },
            error: function (response) {
                error = "Failed.";
                swal({
                    title: "<small class='text-danger'>Error!</small>",
                    type: "error",
                    text: error,
                    timer: 2000,
                    html: true,
                });
            }
        });
    }
</script>
@endsection