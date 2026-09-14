@extends('admin.layouts.masterIndexSalesReturn')

@section('custom_css')
<style type="text/css">
    .table th {
        background: #00c292;
        text-align: center;
    }

</style>
@endsection

@section('card_body')
<form action="{{ route('salesReturn.index') }}" method="get">

    <input type="hidden" name="searched" value="true">

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label for="productType">Product Type</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="productType" name="productType" required>
                        <option value="">Select Product Type</option>
                        @foreach ($productTypes as $key => $value)
                        <option value="{{ $key }}" @if($key == $type) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <label for="dealer">Dealer</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="dealer" name="dealer" required>
                        @foreach ($dealers as $dealerInfo)
                        <option value="{{ $dealerInfo->id }}" @if ($dealerInfo->id == $fdealer)
                            selected
                            @endif
                            >{{ $dealerInfo->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="dealer">Product</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="product" name="product[]" multiple>
                        @foreach ($products as $product)
                        <?php
                        $select = '';
                        if ($fproduct) {
                            if (in_array($product->id, $fproduct)) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        }
                        ?>
                        <option value="{{ $product->id }}" {{ $select }}>{{ $product->name }} - {{ $product->model_no }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-md-12 d-flex justify-content-end mt-4">
                <button type="submit" id="search"
                        class="btn btn-outline-info btn-lg waves-effect search"><i
                        class="fa fa-search"></i> Search</button>

                </form>

                <form action="{{ route('salesReturn.multiple') }}" method="get">

                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect ml-2"> <i class="fa fa-save"></i> Return</button>
            </div>
        </div>

    </div>
    <div class="card-body">
        <div class="table-responsive">
            @php
            $sl = 0;
            @endphp
            <input type="hidden" name="productType" value="{{ $type }}">
            <div class="table-responsive">
                @if($type == 'warranty_product')
                <table class="table table-bordered table-striped" name="productIssueTable">
                    <thead>
                        <tr>
                            <th width="5%">SL</th>
                            <th width="20%">Dealer Name</th>
                            <th width="15%">Product Name</th>
                            <th width="20%">Model No.</th>
                            <th width="20%">Serial No.</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="">
                        <?php
                        $sl = 1;
                        ?>
                        @foreach ($allSales as $allSale)
                        <tr class="row_{{ $allSale->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ @$allSale->issue->dealer->name }}</td>
                            <td>{{ @$allSale->product->name }}</td>
                            <td>{{ @$allSale->product->model_no }}</td>
                            <td>{{ $allSale->serial_no }}</td>
                            <td class="text-nowrap text-center">
                                <?php echo \App\Link::action($allSale->id); ?>
                                <input type="checkbox" name="ids[]" value="{{ $allSale->id }}" </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <table class="table table-bordered table-striped" name="productIssueTable">
                    <thead>
                        <tr>
                            <th width="5%">SL</th>
                            <th width="20%">Dealer Name</th>
                            <th width="15%">Product Name</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="">
                        <?php
                        $sl = 1;
                        ?>
                        @foreach ($allSales as $allSale)
                        <tr class="row_{{ $allSale->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ @$allSale->issue->dealer->name }}</td>
                            <td>{{ @$allSale->product->name }}</td>
                            <td class="text-nowrap text-center">
                                <?php echo \App\Link::action($allSale->id); ?>
                                <input type="checkbox" name="ids[]" value="{{ $allSale->id }}" </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            <div class="mt-5">
                <button class="btn btn-outline-info btn-lg" style="float:right">Return</button>
            </div>

            </form>

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

                productIssueId = $(this).parent().data('id');
                // console.log(liftingId);
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
                                    url: "{{ route('productIssue.delete') }}",
                                    data: {
                                        productIssueId: productIssueId
                                    },

                                    success: function (response) {
                                        swal({
                                            title: "<small class='text-success'>Success!</small>",
                                            type: "success",
                                            text: "Issued product Deleted Successfully!",
                                            timer: 1000,
                                            html: true,
                                        });
                                        $('.row_' + productIssueId).remove();
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
                            } else {
                                swal({
                                    title: "Cancelled",
                                    type: "error",
                                    text: "Issued Product Is Safe :)",
                                    timer: 1000,
                                    html: true,
                                });
                            }
                        });
            });
        });
    </script>

    <script>
        $('#productType').change(function () {
            var type = $(this).val();
            $('#product option').remove();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('product.type') }}",
                data: {
                    type: type,
                },
                success: function (response) {
                    response.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                        $('#product').append(option);
                        $('.chosen-select').chosen();
                        $('.chosen-select').trigger("chosen:updated");
                    });
                }
            });
            $('.chosen-select').chosen();
            $('.chosen-select').trigger("chosen:updated");
        });
    </script>
    @endsection
