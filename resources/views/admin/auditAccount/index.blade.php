@extends('admin.layouts.master')

@section('content')
    <?php use App\CustomerAgreement; ?>
    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <h1 class="text-center h3">{{ $title }}</h1>

            <form action="{{ route('notAudit.print') }}" method="post" enctype="multipart/form-data"
                class="form-horizontal" target="_blank">
                {{ csrf_field() }}

                <input type="hidden" name="reference" value="{{ @$referenceId }}">
                <button type="submit" class="float-right btn btn-outline-info btn-lg">Print</button>
            </form>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="reference">Reference</label>
                        <select name="reference" id="reference" class="form-control chosen-select">
                            <option value="">Select Reference</option>
                            @foreach ($references as $reference)
                                <option value="{{ $reference->id }}" @if ($referenceId == $reference->id) selected @endif>
                                    {{ $reference->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-2">

        <div class="col-md-12">
            <div class="tableFixHead">
                <table id="dataTable" class="table table-bordered table-sm dataTable no-footer">
                    <thead>
                        <tr class="text-center">
                            <th style="width:50px;">SL#</th>
                            <th style="width:105px;">A/C No.</th>
                            <th style="width:120px;">Customer Name</th>
                            <th style="width:105px;">Reference</th>
                            <th style="width:92px;">Is Audit</th>
                            <th style="width:92px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['customers'] as $customer)


                            <?php $referer = CustomerAgreement::where('customer_id', $customer->id)->first();
                            ?>

                            <tr id="tr_{{ $customer->id }}">
                                <td></td>
                                <td>{{ @$customer->code }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $referer['employee_name'] }}</td>
                                <td>
                                    <input type="checkbox" class="form-control" id="audit_{{ $customer->id }}" @if ($customer->is_audit) checked @endif>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-info btn-lg"
                                        onclick="UpdateClient('{{ $customer->id }}', $('#reference_{{ $customer->id }}').val(), $('#audit_{{ $customer->id }}').is(':checked'))">Update</button>
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
        $(function() {
            $('#reference').change(function(e) {
                e.preventDefault();

                let referenceId = $('#reference').val();

                window.location.href = '{{ route('audit.index') }}' + "?reference=" +
                    referenceId;

            });

        });


        function UpdateClient(customerId, reference, isAudit) {

            if (reference == '') {
                alert('Please select reference');
                return true;
            }
          
          if (isAudit == false) {
                swal({
                        title: "Audit Not Selected",
                        type: "warning",
                        timer: 1000
                    },

                );
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('audit.update') }}",
                data: {
                    customerId: customerId,
                    reference: reference,
                    isAudit: isAudit,
                },

                success: function(response) {
                    swal("Audit Successfully", "", "success");
                    $('#tr_' + customerId).remove();
                },
                error: function(response) {
                    console.log(response);
                }
            });


        }

    </script>
@endsection
