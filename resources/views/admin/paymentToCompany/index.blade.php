@extends('admin.layouts.masterIndex')

@section('card_body')
<div class="card-body">
    <div class="table-responsive">
        @php
        $sl = 0;
        @endphp

        <table id="dataTable" class="table table-bordered table-striped"  name="paymentToCompanyTable">
            <thead>
                <tr>
                    <th width="20px">SL</th>
                    <th>Vendor</th>
                    <th>Payment No.</th>
                    <th>Payment Date</th>
                    <th>Payment Type</th>
                    <th>Money Receipt</th>
                    <th>Payment</th>
                    <th width="20px">Action</th>
                </tr>
            </thead>
            <tbody id="">
                @php
                $sl = 0;
                @endphp
                @foreach ($paymentToCompany as $payment)
                <tr class="row_{{ $payment->id }}">
                    <td>{{ $sl++ }}</td>
                    <td>{{ $payment->vendorName }}</td>
                    <td>{{ $payment->payment_no }}</td>
                    <td>{{ date('d-m-Y', strtotime($payment->payment_date))}}</td>
                    <td>{{ $payment->payment_type }}</td>
                    <td>{{ $payment->money_receipt }}</td>
                    <td>{{ $payment->payment_now }}</td>
                    <td>
                        @php
                        echo \App\Link::action($payment->id);
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

            paymentToCompanyId = $(this).parent().data('id');
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
                                url: "{{ route('paymentToCompany.delete') }}",
                                data: {paymentToCompanyId: paymentToCompanyId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Bank Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + paymentToCompanyId).remove();
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
                                text: "This Payment To Company User Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });
</script>
@endsection