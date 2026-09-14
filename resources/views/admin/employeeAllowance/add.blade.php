@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single {
            height: 35px !important;
        }

    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('employeeId') ? ' has-danger' : '' }}">
                    <label for="employee-id">Employee</label>
                    <select class="form-control chosen-select employee" name="employeeId">
                        <option value="">Select Employee</option>
                        @foreach ($employies as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->code }})</option>
                        @endforeach
                    </select>
                    @if ($errors->has('employeeId'))
                        @foreach ($errors->get('employeeId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('allowanceId') ? ' has-danger' : '' }}">
                    <label for="allowance-id">Allowance type</label>
                    <select class="form-control chosen-select allowance" name="allowanceId">
                        <option value="">Select Allowance</option>
                        @foreach ($allowances as $allowance)
                            <option value="{{ $allowance->id }}">{{ $allowance->title }} ({{ $allowance->type }})
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('allowanceId'))
                        @foreach ($errors->get('allowanceId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('allowanceAmount') ? ' has-danger' : '' }}">
                    <label for="employee-name">Amount</label>
                    <input type="text" class="form-control" id="allowanceAmount" name="allowanceAmount"
                        placeholder="Amount" value="">
                    @if ($errors->has('allowanceAmount'))
                        @foreach ($errors->get('allowanceAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


            <div class="col-md-3">
                <label for=""></label>
                <div class="form-group">
                    <input type="hidden" class="row_count" value="0">
                    <span class="btn btn-outline-success add_item" style="width: 100%;">
                        <i class="fa fa-arrow-down"></i> Add
                    </span>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-md-12">
                <label for=""></label>
                <div class="form-group">
                    <table class="table table-bordered table-striped gridTable">
                        <thead>
                            <tr>
                                <th>AllowanceName</th>
                                <th width="250px">Allowance Type</th>
                                <th width="120px">AllowanceAmount</th>
                                <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <td align="right">
                                    <p style="font-size: 14px; font-weight: bold; padding-right: 5px;">Total</p>
                                </td>
                                <td></td>
                                <td>
                                    <input style="text-align: right;" class="form-control totalAdditionAmount"
                                        id='totalAdditionAmount' type="number" name="totalAdditionAmount" value="" readonly>
                                </td>
                            </tr>


                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('custom-js')
    <script>
        $(".add_item").click(function() {
            var allowanceId = $(".allowance option:selected").val();


            var employee = $(".employee").val();
            if (!employee) {
                swal("Please! Select Employee", "", "warning");
                return;
            }
            
            if (allowanceId == "") {
                swal("Please! Select Allowance", "", "warning");
            } else {
                var allowanceType = $('.allowance').find('option:selected').text();
                var allowanceName = $("#allowanceName").val();
                var allowanceAmount = $("#allowanceAmount").val();


                if (!allowanceAmount) {
                    swal("Give Allowance Amount", "", "warning");
                    return;
                }


                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (allowanceId != '') {
                    $.ajax({
                        type: 'post',
                        url: '{{ route('employeeAllowance.getTypeInfo') }}',
                        data: {
                            allowanceId: allowanceId
                        },
                        success: function(data) {
                            var allowance = data.allowance;
                            $(".gridTable tbody").append(
                                '<tr class="itemRow" id="itemRow_' + total + '">' +
                                '<td>' +
                                '<input class="allowanceId_' + total +
                                '" type="hidden" name="allowanceId[]" value="' + allowanceId +
                                '">' +
                                '<input class="form-control allowanceName_' + total +
                                '" type="text" name="allowanceName[]" value="' + allowance.title +
                                '" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input class="form-control allowanceType_' + total +
                                '" type="text" name="allowanceType[]" value="' + allowance.type +
                                '" readonly>' +
                                '</td>' +
                                // '<td>'+
                                //     '<input class="form-control allowanceAmount_'+total+'" type="text" name="allowanceAmount[]" value="'+allowanceAmount+'" readonly>'+
                                // '</td>'+
                                '<td>' +
                                '<input class="form-control ' + allowance.type +
                                ' allowanceAmount_' + total +
                                '" style="text-align: right;" type="text" name="allowanceAmount[]" value="' +
                                allowanceAmount + '" oninput="findTotal()">' +
                                '</td>' +
                                // '<td>'+
                                //     '<input class="form-control subtractionAmount subtractionAmount_'+total+'" style="text-align: right;" type="text" name="subtractionAmount[]" value="0" oninput="findTotal()">'+
                                // '</td>'+
                                '<td align="center">' +
                                '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove(' +
                                total + ')" style="width: 100%;">' +
                                '<i class="fa fa-trash"></i>' +
                                '</span>' +
                                '</td>' +
                                '</tr>'
                            );
                            // $('.allowance option[value='+allowanceId+']').remove();
                            // $('.allowance').trigger('chosen:updated');
                            $('.row_count').val(total);

                            findTotal();
                        }
                    });
                }

            }

        });

        function findTotal() {

            var totalAdditionAmount = 0;
            var totalSubtractionAmount = 0;
            // var payableAmount = 0;

            $(".Addition").each(function() {
                var additionAmount = parseFloat($(this).val());
                totalAdditionAmount += isNaN(additionAmount) ? 0 : additionAmount;
            });

            $(".Subtraction").each(function() {
                var subtractionAmount = parseFloat($(this).val());
                totalSubtractionAmount += isNaN(subtractionAmount) ? 0 : subtractionAmount;
            });

            var net_total = totalAdditionAmount - totalSubtractionAmount;

            $('.totalAdditionAmount').val(net_total);
        }


        function itemRemove(i) {
            var totalAdditionAmount = parseFloat($('.totalAdditionAmount').val());


            var allowanceId = $('.allowanceId_' + i).val();
            var allowanceName = $('.allowanceName_' + i).val();

            $("#itemRow_" + i).remove();

            findTotal();
        }
    </script>
@endsection
