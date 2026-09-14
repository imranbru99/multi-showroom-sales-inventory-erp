@extends('admin.layouts.master')

@section('content')

    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <h1 class="text-center h3">{{ $title }}</h1>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <form class="">
                    <div class="form-group">
                        <label for="reference">Reference</label>
                        <div class="d-flex justify-content-between">
                            <select name="reference" id="reference" class="form-control chosen-select">
                                <option value="">Select Reference</option>
                                <option value="no_refence" <?= !empty($no_refer) && $no_refer == 'no_refence' ? 'selected' : '' ?>>No Reference</option>
                                            @foreach ($references as $reference)
                                                <option value="{{ $reference->id }}" @if ($referenceId == $reference->id) selected @endif>
                                                    {{ $reference->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-info"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                    </form>
                            </div>
                            <div class="col-md-8 d-flex justify-content-end">
                                <form class="">
                                    <div class="form-group">
                                        <label for="reference">Account No</label>
                                        <div class=" d-flex justify-content-between">
                                            <input type="number" class="form-control" name="account_no" value="{{ @$account_no }}">
                                            <button type="submit" class="btn btn-outline-info"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">

                        <div class="col-md-12">
                            <div class="tableFixHead">
                                <table id="dataTable" class="table table-bordered table-sm dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">SL#</th>
                                            <th style="width:105px;">A/C No.</th>
                                            <th style="width:120px;">Customer Name</th>
                                            <th style="width:105px;">Reference</th>
                                            <th style="width:92px;">Freeze</th>
                                            <th style="width:92px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['customers'] as $customer)
                                        @php
                                            $refer = \App\CustomerAgreement::where('customer_id', @$customer->id)->first();
                                        @endphp
                                            <tr id="tr_{{ $customer->id }}">
                                                <td></td>
                                                <td>{{ @$customer->code }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>
                                                    <select name="reference" id="reference_{{ $customer->id }}"
                                                        class="form-control chosen-select">
                                                        <option value="">Select Reference</option>
                                                        @foreach ($references as $reference)

                                                        @php
                                                            $select = '';
                                                            if (!empty($referenceId) && $referenceId == $reference->id) {
                                                                $select = 'selected';
                                                            } elseif (!empty($account_no) && $reference->id == $refer->employee_id) {
                                                                $select = 'selected';
                                                            } else {
                                                                $select = '';
                                                            }
                                                        @endphp
                                                            <option value="{{ $reference->id }}" {{ $select }}>
                                                                {{ $reference->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="form-control" id="freeze_{{ $customer->id }}" @if ($customer->freeze) checked @endif>
                                                </td>
                                                <td>
                                                    <button class="btn btn-outline-info btn-lg"
                                                        onclick="UpdateClient('{{ $customer->id }}', $('#reference_{{ $customer->id }}').val(), $('#freeze_{{ $customer->id }}').is(':checked'))">Update</button>
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
                    // $(function() {
                    //     $('#reference').change(function(e) {
                    //         e.preventDefault();

                    //         let referenceId = $('#reference').val();

                    //         window.location.href = '{{ route('quick.client.update') }}' + "?reference=" +
                    //             referenceId;

                    //     });

                    // });


                    function UpdateClient(customerId, reference, freezeStatus) {

                        if (reference == '') {
                            alert('Please select reference');
                            return true;
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "{{ route('update.client.references') }}",
                            data: {
                                customerId: customerId,
                                reference: reference,
                                freezeStatus: freezeStatus,
                            },

                            success: function(response) {

                                console.log(response);
                                swal({
                                    title: "Updated Successfully",
                                    type: "success",
                                    timer: 1000,
                                });
                            },
                            error: function(response) {
                                console.log(response);
                            }
                        });

                    }
                </script>
@endsection
