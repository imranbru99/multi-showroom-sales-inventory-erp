@extends('admin.layouts.masterIndex')
@php
$advance_remarks = ['advance', 'Advance From Collection', 'Advance From Return'];
@endphp
@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped" name="dealerTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Dealer Name</th>
                        <th width="70px">Contact</th>
                        <th width="100px">Payment No</th>
                        <th width="100px">Date</th>
                        <!--        <th width="110px">Money Reciept</th> -->
                        <th width="90px">Type</th>
                        <th width="90px">Payment</th>
                        <th width="200px">Remarks</th>
                        <th width="200px">User</th>
                        <th width="70px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($dealerCollections as $dealerCollection)
                        <tr class="row_{{ $dealerCollection->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ $dealerCollection->dealerName }}</td>
                            <td>{{ $dealerCollection->dealerMobile }}</td>
                            <td>{{ $dealerCollection->payment_no }}</td>
                            <td>{{ date('d-m-Y', strtotime($dealerCollection->payment_date)) }}</td>
                            <td>{{ $dealerCollection->money_receipt_type }}</td>
                            <td align="right">{{ round($dealerCollection->payment_amount, 2) }}</td>
                            <td>{{ $dealerCollection->remarks }}</td>
                            <td>{{ $dealerCollection->user->name }}</td>
                            <td>
                                 @php
                                    echo \App\Link::action($dealerCollection->id);
                                @endphp
                            </td>
                        </tr>
                    @endforeach

                    @foreach ($advanceCollections as $advanceCollection)
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $advanceCollection->dealer->name }}</td>
                            <td>{{ $advanceCollection->dealer->mobile }}</td>
                            <td>{{ $advanceCollection->payment_no }}</td>
                            <td>{{ date('d-m-Y', strtotime($advanceCollection->date)) }}</td>
                            <td>{{ $advanceCollection->type }}</td>
                            <td align="right">
                                @if ($advanceCollection->advance_amount > 0)
                                    {{ $advanceCollection->advance_amount }}
                                @else
                                    {{ $advanceCollection->adjust_amount }}
                                @endif
                            </td>
                            <td>{{ $advanceCollection->remarks }}</td>
                            <td>{{ $advanceCollection->user->name }}</td>
                            <td>

                                @if (in_array($advanceCollection->remarks, $advance_remarks))
                                    <a onclick='deleteAdvance(event, "{{ route("dealerAdvance.delete", $advanceCollection->id) }}")'
                                        href="#"
                                        data-original-title="delete">
                                        <i class="fa fa-trash text-inverse m-r-10"></i>
                                    </a>
                                @endif
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
        function deleteAdvance(e, url) {
            e.preventDefault();
            let c = confirm('Are you sure you want to delete');
            
            // console.log(url);

            if (c) {
                window.location.href = url;
            }

            return true;
        }


        $(document).ready(function() {
            var updateThis;

            //ajax delete code
            $('#dataTable tbody').on('click', 'i.fa-trash', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                dealerCollectionId = $(this).parent().data('id');
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
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('dealerCollection.delete') }}",
                                data: {
                                    dealerCollectionId: dealerCollectionId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Dealer Collection Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + dealerCollectionId).remove();
                                },
                                error: function(response) {
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
                                text: "Dealer Collection Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });
        });

    </script>
@endsection
