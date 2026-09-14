@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <input class="form-control" type="hidden" name="employeeAllowanceId" value="{{ $employeeAllowance->id }}">

        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('employeeId') ? ' has-danger' : '' }}">
                <label for="employee-id">Employee</label>
                <select class="form-control chosen-select employee" name="employeeId">
                    <option value="">Select Employee</option>
                    @foreach ($employies as $employee)
                    @php
                    if ($employee->id == $employeeAllowance->staff_id)
                    {
                    $select = "selected";
                    }
                    else
                    {
                    $select = "";
                    }
                    @endphp
                    <option value="{{ $employee->id }}" {{ $select }}>{{ $employee->name }} ({{ $employee->code }})</option>
                    @endforeach
                </select>
                @if ($errors->has('employeeId'))
                @foreach($errors->get('employeeId') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <!-- <div class="col-md-3">
            <div class="form-group {{ $errors->has('employeeName') ? ' has-danger' : '' }}">
                <label for="employee-name">Employee Name</label>
                <input type="text" class="form-control" id="employeeName" name="employeeName" placeholder="Employee Name" value="{{ $employeeAllowance->employeeName }}" readonly>
                @if ($errors->has('employeeName'))
                    @foreach($errors->get('employeeName') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div> -->

        <!--            <div class="col-md-3">
                        <div class="form-group {{ $errors->has('employeeDesignation') ? ' has-danger' : '' }}">
                            <label for="employeeDesignation">Designation</label>
                            <input type="text" class="form-control" id="employeeDesignation" name="employeeDesignation" placeholder="employeeDesignation" value="{{ $employeeAllowance->employee_designation }}" >
                            @if ($errors->has('employeeDesignation'))
                            @foreach($errors->get('employeeDesignation') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                            @endif
                        </div>
                    </div>-->

        <!--            <div class="col-md-3">
                        <div class="form-group {{ $errors->has('allowanceName') ? ' has-danger' : '' }}">
                            <label for="allowance-name">Allowance Name</label>
                            <input type="text" class="form-control" id="allowanceName" name="allowanceName" placeholder="Allowance Name" value="{{ $employeeAllowance->allowance_name }}" >
                            @if ($errors->has('allowanceName'))
                            @foreach($errors->get('allowanceName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                            @endif
                        </div>
                    </div>-->

        <div class="col-md-3">
            <div class="form-group {{ $errors->has('allowanceId') ? ' has-danger' : '' }}">
                <label for="allowance-id">Allowance type</label>
                <select class="form-control chosen-select allowance" name="allowanceId">
                    <option value="">Select Allowance</option>

                    @foreach ($allowances as $allowance)
                    <?php
                    if ($allowance->id == $allowancesList->allowance_type) {
                        $select = "selected";
                    } else {
                        $select = "";
                    }
                    ?>
                    <option value="{{ $allowance->id }}" {{ $select }}>{{ $allowance->title }} ({{ $allowance->type }})</option>
                    @endforeach
                </select>
                @if ($errors->has('allowanceId'))
                @foreach($errors->get('allowanceId') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group {{ $errors->has('allowanceAmount') ? ' has-danger' : '' }}">
                <label for="employee-name">Amount</label>
                <input type="text" class="form-control" id="allowanceAmount" name="allowanceAmount" placeholder="Amount" value="" >
                @if ($errors->has('allowanceAmount'))
                @foreach($errors->get('allowanceAmount') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <!--    <div class="col-md-3">
               <label for="salary-type">Salary Type</label>
               <div class="form-group" style="height: 40px; line-height: 40px;">
                   <div class="form-check-inline">
                       <label class="form-check-label" for="yes">
                           <input type="radio" class="form-check-input salaryType" name="salaryType" value="Fixed" {{ $employeeAllowance->salary_type == "Fixed" ? 'checked' : 'disabled'}} >Fixed
                       </label>
                   </div>
                   <div class="form-check-inline">
                       <label class="form-check-label" for="no">
                           <input type="radio" class="form-check-input salaryType" name="salaryType" value="Gross" {{ $employeeAllowance->salary_type == "Gross" ? 'checked' : 'disabled'}} >Gross
                       </label>
                   </div>
               </div>
           </div> -->
        <div class="col-md-3">
            <label for=""></label>
            <div class="form-group">
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
                <table class="table table-bordered table-striped gridTable" >
                    <thead>
                        <tr>
                            <th>AllowanceName</th>
                            <th width="250px">Allowance Type</th>
                            <th width="120px">AllowanceAmount</th>
                            <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                        </tr>
                    </thead>

                    <tbody id="tbody">

                        @php
                        $i = 0;
                        @endphp
                        @foreach ($dealers as $dealer)
                        @php
                        $i++;
                        @endphp
                        <tr id="itemRow_{{ $i }}">
                            <td>
                                <input class="allowanceId__{{ $i }}" type="hidden" name="allowanceId[]" value="{{ $dealer->allowance_type }}">
                                <input class=" allowanceName_{{ $i }}" type="text" name="allowanceName[]" value="{{ $dealer->allowance_name}}" readonly>
                            </td>                
                            <td>
                                <input class="allowanceType_{{ $i }}" type="text" name="allowanceType[]" value="{{ $dealer->all_type }}" required readonly>
                            </td>
                            <td>
                                <input class="allowanceAmount allowanceAmount_{{ $i }}" style="text-align: right;" type="text" name="allowanceAmount[]" value="{{ $dealer->allowance_amount}}" oninput="findTotal()"required >
                            </td>


                            <td align="center">
                                <span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove({{ $i }})" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td align="right" ><p style="font-size: 14px; font-weight: bold; padding-right: 5px;">Total</p></td>
                            <td></td>
                            <td>
                                <input style="text-align: right;" class="form-control totalAdditionAmount"id='totalAdditionAmount' type="number" name="totalAdditionAmount" value="{{ $employeeAllowance->total_amount }}" readonly>
                            </td>
                        </tr>


                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!--  <div class="fixed"> -->
    <div class="row">
        <!--   <div class="col-md-6">
              <div class="form-group {{ $errors->has('allowanceId') ? ' has-danger' : '' }}">
                  <label for="allowance-name">Allowance Name</label>
                  <select class="form-control chosen-select allowance" name="allowanceId">
                      <option value="">Select Leave Name</option>
                      @foreach ($allowances as $allowance)
                          <option value="{{ $allowance->id }}">{{ $allowance->title }}</option>
                      @endforeach
                  </select>
                  @if ($errors->has('allowanceId'))
                      @foreach($errors->get('allowanceId') as $error)
                          <div class="form-control-feedback">{{ $error }}</div>
                      @endforeach
                  @endif
              </div>
          </div> -->


    </div>

    <!-- <div class="row">
        <div class="col-md-12">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-bordered table-striped gridTable" >
                    <thead>
                        <tr>
                            <th>Allowance Name</th>
                            <th width="120px">Addition</th>
                            <th width="120px">Subtraction</th>
                            <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @php
                            $i = 0;
                        @endphp
                        @if ($employeeAllowance->salary_type == "Gross")
                            @foreach ($employeeAllowanceLists as $employeeAllowanceList)
                                @php
                                    $i++;
                                @endphp
                                <tr class="itemRow" id="itemRow_{{ $i }}}">
                                    <td>
                                        <input class="allowanceId_{{ $i }}}" type="hidden" name="allowanceId[]" value="{{ $employeeAllowanceList->allowance_id }}">
                                        <input class="form-control allowanceName_{{ $i }}}" type="text" name="allowanceName[]" value="{{ $employeeAllowanceList->allowanceName }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control additionAmount additionAmount_{{ $i }}}" style="text-align: right;" type="text" name="additionAmount[]" value="{{ $employeeAllowanceList->addition_amount }}" oninput="findTotal()">
                                    </td>
                                    <td>
                                        <input class="form-control subtractionAmount subtractionAmount_{{ $i }}}" style="text-align: right;" type="text" name="subtractionAmount[]" value="{{ $employeeAllowanceList->subtraction_amount }}" oninput="findTotal()">
                                    </td>
                                    <td align="center">
                                        <span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove({{ $i }}})" style="width: 100%;">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>

                    <tfoot>
                        <tr>
                            <td align="right"><p style="font-size: 14px; font-weight: bold; padding-right: 5px;">Summery</p></td>
                            <td>
                                <input style="text-align: right;" class="form-control totalAdditionAmount" style="text-align: right;" type="number" name="totalAdditionAmount" value="{{ $employeeAllowance->total_addition_amount }}" readonly>
                            </td>
                            <td>
                                <input style="text-align: right;" class="form-control totalSubtractionAmount" style="text-align: right;" type="number" name="totalSubtractionAmount" value="{{ $employeeAllowance->total_subtraction_amount }}" readonly>
                            </td>
                        </tr>

                        <tr>
                            <td align="right"><p style="font-size: 14px; font-weight: bold; padding-right: 5px;">Payable Amount</p></td>
                            <td colspan="2">
                                <input style="text-align: right;" class="form-control payableAmount" type="number" name="payableAmount" value="{{ $employeeAllowance->payable_amount }}" readonly>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" class="row_count" value="{{ $i }}">
            </div>
        </div>
    </div> -->
    <!--  </div> -->

    <!--   <div class="row gross">
          <div class="col-md-6">
              <div class="form-group {{ $errors->has('allowanceName') ? ' has-danger' : '' }}">
                  <label for="designation">Allowance Name</label>
                  <input type="hidden" class="form-control" id="fixedAllowanceId" name="fixedAllowanceId" value="6" readonly>
                  <input type="text" class="form-control" id="fixedAllowanceName" name="fixedAllowanceName" placeholder="Allowance Name" value="Fixed" readonly>
                  @if ($errors->has('allowanceName'))
                      @foreach($errors->get('allowanceName') as $error)
                          <div class="form-control-feedback">{{ $error }}</div>
                      @endforeach
                  @endif
              </div>
          </div>

          <div class="col-md-6">
              <div class="form-group {{ $errors->has('fixedAmount') ? ' has-danger' : '' }}">
                  <label for="designation">Amount</label>
                  <input type="number" class="form-control" id="fixedAmount" name="fixedAmount" placeholder="Fixed Amount" value="{{ $employeeAllowance->salary_type == "Fixed" ? $employeeAllowanceLists->addition_amount : 0 }}">
                  @if ($errors->has('fixedAmount'))
                      @foreach($errors->get('fixedAmount') as $error)
                          <div class="form-control-feedback">{{ $error }}</div>
                      @endforeach
                  @endif
              </div>
          </div>
      </div> -->
</div>
@endsection

@section('custom-js')
<script>

    /*  $(document).ready(function(){
     var salaryType = $("input[name='salaryType']:checked").val();
     
     if (salaryType == "Fixed")
     {
     $('.itemRow').remove();
     $('.allowance').prop('selectedIndex',0);
     $('.allowance').trigger('chosen:updated');
     $('.fixed').hide();
     $('.gross').show();
     }
     
     if (salaryType == "Gross")
     {
     $('.fixed').show();
     $('.gross').hide();
     $('#fixedAmount').val("");
     }
     });  */

    // $(document).on('change', '.employee', function(){
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });

    //     var employeeId = $('.employee option:selected').val();

    //     if (employeeId == "")
    //     {
    //         $('#employeeName').val("");
    //         $('#designation').val("");
    //     }
    //     else
    //     {
    //         $.ajax({
    //             type:'post',
    //             url:'{{ route('employeeAllowance.getEmployeeInfo') }}',
    //             data:{employeeId:employeeId},
    //             success:function(data){
    //                 var employee = data.employee;

    //                 $('#employeeName').val(employee.name);
    //                 $('#designation').val(employee.designationName);
    //             }
    //         });
    //     }
    // });

    // $(".add_item").click(function () {
    //     var allowanceId = $(".allowance option:selected").val();

    //     if (allowanceId == "")
    //     {
    //         swal("Please! Select A Product", "", "warning");
    //     }
    //     else
    //     {
    //         var allowanceName = $('.allowance').find('option:selected').text();
    //         var row_count = $('.row_count').val();
    //         var total = parseInt(row_count) + 1;

    //         $(".gridTable tbody").append(
    //             '<tr class="itemRow" id="itemRow_' + total + '">' +                
    //                 '<td>'+
    //                     '<input class="allowanceId_'+total+'" type="hidden" name="allowanceId[]" value="'+allowanceId+'">'+
    //                     '<input class="form-control allowanceName_'+total+'" type="text" name="allowanceName[]" value="'+allowanceName+'" readonly>'+
    //                 '</td>'+
    //                 '<td>'+
    //                     '<input class="form-control additionAmount additionAmount_'+total+'" style="text-align: right;" type="text" name="additionAmount[]" value="0" oninput="findTotal()">'+
    //                 '</td>'+
    //                 '<td>'+
    //                     '<input class="form-control subtractionAmount subtractionAmount_'+total+'" style="text-align: right;" type="text" name="subtractionAmount[]" value="0" oninput="findTotal()">'+
    //                 '</td>'+
    //                 '<td align="center">'+
    //                     '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove('+total+')" style="width: 100%;">'+
    //                         '<i class="fa fa-trash"></i>'+
    //                     '</span>'+
    //                 '</td>'+
    //             '</tr>'
    //         );
    //         // $('.allowance option[value='+allowanceId+']').remove();
    //         // $('.allowance').trigger('chosen:updated');
    //         $('.row_count').val(total);
    //     }
    // });


    $(".add_item").click(function () {
    var allowanceId = $(".allowance option:selected").val();
    if (allowanceId == "")
    {
    swal("Please! Select Allowance", "", "warning");
    } else
    {
    var allowanceType = $('.allowance').find('option:selected').text();
    var allowanceName = $("#allowanceName").val();
    var allowanceAmount = $("#allowanceAmount").val();
//            var allowanceTypeId = $('.allowance').find('option:selected').val();

    var row_count = $('.row_count').val();
    var total = parseInt(row_count) + 1;
//            console.log(allowanceId);
    // console.log(allowanceName);
    // console.log(allowanceAmount);
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
            success: function (data) {
            var allowance = data.allowance;
            $(".gridTable tbody").append(
                    '<tr class="itemRow" id="itemRow_' + total + '">' +
                    '<td>' +
                    '<input class="allowanceId_' + total + '" type="hidden" name="allowanceId[]" value="' + allowanceId + '">' +
                    '<input class="form-control allowanceName_' + total + '" type="text" name="allowanceName[]" value="' + allowance.title + '" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input class="form-control allowanceType_' + total + '" type="text" name="allowanceType[]" value="' + allowance.type + '" readonly>' +
                    '</td>' +
                    // '<td>'+
                    //     '<input class="form-control allowanceAmount_'+total+'" type="text" name="allowanceAmount[]" value="'+allowanceAmount+'" readonly>'+
                    // '</td>'+
                    '<td>' +
                    '<input class="form-control allowanceAmount allowanceAmount_' + total + '" style="text-align: right;" type="text" name="allowanceAmount[]" value="' + allowanceAmount + '" oninput="findTotal()">' +
                    '</td>' +
                    // '<td>'+
                    //     '<input class="form-control subtractionAmount subtractionAmount_'+total+'" style="text-align: right;" type="text" name="subtractionAmount[]" value="0" oninput="findTotal()">'+
                    // '</td>'+
                    '<td align="center">' +
                    '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove(' + total + ')" style="width: 100%;">' +
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
    function findTotal()
    {

    var totalAdditionAmount = 0;
    // var totalSubtractionAmount = 0;            
    // var payableAmount = 0;

    $(".allowanceAmount").each(function () {
    var allowanceAmount = parseFloat($(this).val());
    totalAdditionAmount += isNaN(allowanceAmount) ? 0 : allowanceAmount;
    });
    // $(".subtractionAmount").each(function () {
    //     var subtractionAmount = parseFloat($(this).val());
    //     totalSubtractionAmount += isNaN(subtractionAmount) ? 0 : subtractionAmount;
    // });

    // payableAmount = totalAdditionAmount - totalSubtractionAmount;

    $('.totalAdditionAmount').val(totalAdditionAmount);
    // $('.totalSubtractionAmount').val(totalSubtractionAmount);
    // $('.payableAmount').val(payableAmount);
    }

//       function findTotal1 (){
//        var total = 0;
//       $("input[id *= 'allowanceAmount']").each(function() {
//         totalAdditionAmount += parseFloat($(this).val());
//     });
//     return roundToTwo(totalAdditionAmount);
//     //return total;
// } 



//         function findTotal(){
//     var arr = document.getElementsByName('allowanceAmount');
//     var tot=0;
//     for(var i=0;i<arr.length;i++){
//         if(parseInt(arr[i].value))
//             tot += parseInt(arr[i].value);
//     }
//     document.getElementById('totalAdditionAmount').value = tot;
// }


    function itemRemove(i) {
    var totalAdditionAmount = parseFloat($('.totalAdditionAmount').val());
    var allowanceId = $('.allowanceId_' + i).val();
    var allowanceName = $('.allowanceName_' + i).val();
    // payableAmount = totalAdditionAmount - totalSubtractionAmount;

    $('.totalAdditionAmount').val(totalAdditionAmount);
    // $('.totalSubtractionAmount').val(totalSubtractionAmount);
    // $('.payableAmount').val(payableAmount);

    // $('.allowance').append(
    //     '<option value="'+allowanceId+'">'+allowanceName+'</option>'
    // );

    // $('.allowance').trigger('chosen:updated');

    $("#itemRow_" + i).remove();
    findTotal();
    }
</script>
@endsection