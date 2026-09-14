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
                <input class="form-control" type="hidden" name="manufactureId" value="{{ $manufacturesetup->id }}">
             

            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                    <label for="employee-id">Product Name</label>
                     <select class="form-control chosen-select product" name="productId">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            @php
                                if ($product->id == $manufacturesetup->product_id)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            @endphp
                            <option value="{{ $product->id }}" {{ $select }}>{{ $product->name }} ( {{ $product->code }} - {{ $product->color }} - {{ $product->model_no }} 
                            )</option>
                        @endforeach
                    </select>
                    @if ($errors->has('productId'))
                        @foreach($errors->get('productId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('catagoryId') ? ' has-danger' : '' }}">
                    <label for="employee-name">Product Catagory</label>
                    <select class="form-control chosen-select catagory" name="catagoryId">
                        <option value="">Select Catagory</option>
                        @foreach ($catgoires as $catgoire)
                         @php
                                if ($catgoire->id == $manufacturesetup->catgorie_id)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            @endphp
                            <option value="{{ $catgoire->id }} " {{ $select }}>{{ $catgoire->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('catagoryId'))
                        @foreach($errors->get('catagoryId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('prouductionUnit') ? ' has-danger' : '' }}">
                    <label for="allowance-name">Production Unit</label>
                    <input type="text" class="form-control" id="prouductionUnit" name="prouductionUnit" placeholder="Production Unit" value="{{$manufacturesetup->product_unit }}">
                    @if ($errors->has('prouductionUnit'))
                        @foreach($errors->get('prouductionUnit') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
                

                <div class="col-md-4">
                <div class="form-group {{ $errors->has('partstId') ? ' has-danger' : '' }}">
                    <label for="employee-name">Parts Name</label>
                    <select class="form-control chosen-select parts" name="partstId">
                        <option value="">Parts Select</option>
                        @foreach ($products as $pro)

                            @php
                                if ($pro->id == $manufacturesetup->product_id)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            @endphp
                            <option value="{{ $pro->id }}"{{ $select }}>{{ $pro->name }} ({{ $pro->code}}-{{$pro->color}}-{{$pro->model_no}})
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('partstId'))
               @foreach($errors->get('partstId') as $error)
                 <div class="form-control-feedback">{{ $error }}</div>
               @endforeach
               @endif
           </div>
       </div>


                       <div class="col-md-4">
                <div class="form-group {{ $errors->has('requiredUnit') ? ' has-danger' : '' }}">
                    <label for="employee-name">Required Unit</label>
                    <input type="text" class="form-control" id="requiredUnit" name="requiredUnit" placeholder="required Unit" value="{{$manufacturesetup->required_unit}}" >
                    @if ($errors->has('requiredUnit'))
               @foreach($errors->get('requiredUnit') as $error)
                 <div class="form-control-feedback">{{ $error }}</div>
               @endforeach
               @endif
                  </div>
                </div>
            

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('partsAmount') ? ' has-danger' : '' }}">
                    <label for="employee-name">Cost Price</label>
                    <input type="text" class="form-control" id="partsAmount" name="partsAmount" placeholder="amount" value="{{$manufacturesetup->total_amount}}" >
                    @if ($errors->has('partsAmount'))
                        @foreach($errors->get('partsAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


        <div class="col-md-4">
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
                        <table class="table table-bordered table-striped gridTable" >
                            <thead>
                                <tr>
                                    <th>Parts Name</th>
                                    <th width="120px">Required Unit</th>
                                    <th width="120px">Cost Value</th>
                                    <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                                </tr>
                            </thead>

                            <tbody id="tbody">
                                 @php
                           $i = 0;
                           @endphp
                           @foreach ($manulistget as $manufac)
                           @php
                           $i++;
                           @endphp
                           <tr id="itemRow_{{ $i }}">                
                            <td>



                                <input class="partstId_{{ $i }}" type="text" name="partstId[]" value="{{ $manufac->name}}" required >


                            </td>
                            <td>

                                <input class=" requiredUnit_{{ $i }}" type="text" name="requiredUnit[]" value="{{ $manufac->required_unit}}" >


                            </td>



                            <td>
                                <input class="partsAmount partsAmount_{{ $i }}" style="text-align: right;" type="text" name="partsAmount[]" value="{{ $manufac->total_amount}}" oninput="findTotal()"required >


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
                                    
                                    <td>
                                        
                                    </td>
                                
                                
                                    <td>
                                        <input style="text-align: right;" class="form-control totalAdditionAmount"id='totalAdditionAmount' type="number" name="totalAdditionAmount" value="" readonly>
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
        // $(document).on('change','#totalAdditionAmount'function(){
        //     var totalAdditionAmount =  $(this).find("selected").attr('allowanceAmount');
        //     var allowanceAmount= $(this).find("totalAdditionAmount");



        //     $('#totalAdditionAmount').html('Tk. '+calculateGTotal());
        // });
        // $('.gross').hide()
        // $('.fixed').hide()

        // $('.salaryType').click(function(event) {
        //     var salaryType = $("input[name='salaryType']:checked").val();
        //     var employeeId = $('.employee option:selected').val();

        //     if (employeeId == "")
        //     {
        //         swal("Please! Select A Employee", "", "warning");
        //         $(this).prop('checked', false);

        //         $('.itemRow').remove();
        //         $('.allowance').prop('selectedIndex',0);
        //         $('.allowance').trigger('chosen:updated');
        //         $('#fixedAmount').val("");

        //         $('.gross').hide()
        //         $('.fixed').hide()
        //     }
        //     else
        //     {
        //         if (salaryType == "Fixed")
        //         {
        //             $('.itemRow').remove();
        //             $('.allowance').prop('selectedIndex',0);
        //             $('.allowance').trigger('chosen:updated');
        //             $('.fixed').hide();
        //             $('.gross').show();
        //         }

        //         if (salaryType == "Gross")
        //         {
        //             $('.fixed').show();
        //             $('.gross').hide();
        //             $('#fixedAmount').val("");
        //         }
        //     }
        // })

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

        $(".add_item").click(function () {
            var partstId = $(".parts option:selected").val();

            if (partstId == "")
            {
                swal("Please! Select A Product", "", "warning");
            }
            else
            {
                var partstId = $('.parts').find('option:selected').text();

                //var partsName = $("#partsName").val();
                var requiredUnit = $("#requiredUnit").val();
                var partsAmount = $("#partsAmount").val();

                // console.log(allowanceType);
                // console.log(allowanceName);
                // console.log(allowanceAmount);

                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1;

                $(".gridTable tbody").append(
                    '<tr class="itemRow" id="itemRow_' + total + '">' +                
                        '<td>'+
                        
                            '<input class="form-control partstId_'+total+'" type="text" name="partstId[]" value="'+partstId+'" readonly>'+
                        '</td>'+
                        '<td>'+
                            '<input class="form-control requiredUnit_'+total+'" type="text" name="requiredUnit[]" value="'+requiredUnit+'" readonly>'+
                        '</td>'+
                        // '<td>'+
                        //     '<input class="form-control allowanceAmount_'+total+'" type="text" name="allowanceAmount[]" value="'+allowanceAmount+'" readonly>'+
                        // '</td>'+
                        '<td>'+
                            '<input class="form-control partsAmount partsAmount_'+total+'" style="text-align: right;" type="text" name="partsAmount[]" value="'+partsAmount+'" oninput="findTotal()">'+
                        '</td>'+
                        // '<td>'+
                        //     '<input class="form-control subtractionAmount subtractionAmount_'+total+'" style="text-align: right;" type="text" name="subtractionAmount[]" value="0" oninput="findTotal()">'+
                        // '</td>'+
                        '<td align="center">'+
                            '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove('+total+')" style="width: 100%;">'+
                                '<i class="fa fa-trash"></i>'+
                            '</span>'+
                        '</td>'+
                    '</tr>'
                );
                // $('.allowance option[value='+allowanceId+']').remove();
                // $('.allowance').trigger('chosen:updated');
                $('.row_count').val(total);
            }

            findTotal();
        });

        function findTotal()
        {
            
            var totalAdditionAmount = 0;            
            // var totalSubtractionAmount = 0;            
            // var payableAmount = 0;

            $(".partsAmount").each(function () {
                var partsAmount = parseFloat($(this).val());
                totalAdditionAmount += isNaN(partsAmount) ? 0 : partsAmount;

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
       

            var catagoryId = $('.catagoryId_'+i).val();
            var partstId = $('.partstId'+i).val();

            // payableAmount = totalAdditionAmount - totalSubtractionAmount;

            $('.totalAdditionAmount').val(totalAdditionAmount);
            // $('.totalSubtractionAmount').val(totalSubtractionAmount);
            // $('.payableAmount').val(payableAmount);

            // $('.allowance').append(
            //     '<option value="'+allowanceId+'">'+allowanceName+'</option>'
            // );
            
            // $('.allowance').trigger('chosen:updated');

            $("#itemRow_" + i).remove();
        }
    </script>
@endsection