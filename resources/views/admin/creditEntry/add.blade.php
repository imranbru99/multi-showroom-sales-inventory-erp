@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="row">
            {{-- <div class="col-md-4">
                <label for="showroom">Showrooms</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="showroom" name="showroom">
                        <option value="">Select A Showroom</option>
                        @foreach ($showrooms as $showroom)
                            <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                        @endforeach
                    </select>
                </div>  
            </div> --}}

            <div class="col-md-4">
                <label for="debit-account-head">Debit Account Head</label>
                <div class="form-group">
                    <select class="chosen-select" id="debitAccountHead" name="debitAccountHead" required="">
                        <option value="">Select Account Name</option>
                        @foreach ($debitCoas as $debitCoas)
                            <option value="{{ $debitCoas->head_code }}">{{ $debitCoas->head_name }}</option>
                        @endforeach
                    </select>
                </div>  
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('voucharNo') ? ' has-danger' : '' }}">
                    <label for="vouchar-no">Vouchar No.</label>
                    <input type="text" class="form-control" id="voucharNo" name="voucharNo" value="{{ $voucherNo }}" placeholder="Select Showroom For Generate Voucher No" readonly>
                    @if ($errors->has('voucharNo'))
                        @foreach($errors->get('voucharNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="transaction-date">Transaction Date</label>
                    <input type="text" class="form-control add_datepicker" name="transactionDate" value="" placeholder="Select Transaction Date" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control remarks" id="remarks" name="remarks" rows="2"></textarea>
                    @if ($errors->has('remarks'))
                        @foreach($errors->get('remarks') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <table class="table table-bordered table-striped gridTable" >
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th width="120px">Credit</th>
                                <th width="90px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <tr>
                                <td>
                                    <select class="chosen-select coa coa_1" id="coa" name="coa[]">
                                        <option value="">Select Account Name</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{ $coa->head_code }}">{{ $coa->head_name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" class="credit credit_1" id="credit" name="credit[]" oninput="findTotal()" value="0">
                                </td>

                                <td align="center">
                                    <input type="hidden" class="row-count" value="1">
                                    <span class="btn btn-outline-success btn-sm add-item" onclick="addItem()" style="width: 100%;">
                                        <i class="fa fa-plus-circle"></i>
                                    </span>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td align="center" style="font-weight: bold;">Total Amount</td>
                                <td><input style="text-align: right" type="number" class="totalCredit" id="totalCredit" name="totalCredit" value="0"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>        
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).on('change', '#showroom', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var showroomId = $('#showroom').val();

            $.ajax({
                type:'post',
                url:'{{ route('creditEntry.getVoucharNo') }}',
                data:{showroomId:showroomId},
                success:function(data){
                    $('#voucharNo').val(data);
                }
            });
        });

        function addItem()
        {
            var rowcount = $('.row-count').val();
            var total = parseInt(rowcount) + 1;

            $(".gridTable tbody").append(
                '<tr id="itemRow_'+total+'">'+
                    '<td>'+
                        '<div id="coa_select_menu_'+total+'">'+                                    
                            '<select class="chosen-select coa coa_'+total+'" id="coa" name="coa[]">'+
                                '<option value="">Select Account Name</option>'+
                            '</select>'+
                        '</div>'+
                    '</td>'+

                    '<td>'+
                        '<input style="text-align: right;" type="number" class="credit credit_'+total+'" id="credit" name="credit[]" oninput="findTotal()" value="0">'+
                    '</td>'+

                    '<td align="center">'+
                        '<span class="btn btn-outline-success btn-sm add-item" onclick="addItem()" style="width: 40px;">'+
                            '<i class="fa fa-plus-circle"></i>'+
                        '</span>'+
                        ' <span class="btn btn-outline-danger btn-sm remove-item" onclick="removeItem('+total+')" style="width: 40px;">'+
                            '<i class="fa fa-minus-circle"></i>'+
                        '</span>'+
                    '</td>'+
                '</tr>'
            );
            $('.row-count').val(total);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('creditEntry.getCoa') }}",
                data:{total:total},
                success: function(data) {
                    $('#coa_select_menu_'+total).html(data);
                    $(".chosen-select").chosen();
                },
                error: function(data) {

                }
            });           
        }

        function findTotal()
        {
            var totalCredit = 0;
            var coaName;
            var remarks = "";
            var loop = $('.row-count').val();

            $(".credit").each(function ()
            {
                var credit = parseInt($(this).val());
                totalCredit += isNaN(credit) ? 0 : credit;
            });

            for (var i = 1; i <= loop; i++)
            {
                coaName = $('.coa_'+i+' option:selected').text();

                if (coaName)
                {
                    if (remarks != "")
                    {
                        remarks += ', ';
                    }
                    
                    remarks += coaName;
                }               
            }

            $('.remarks').val(remarks);
            $('#totalCredit').val(totalCredit);           
        }

        function removeItem(i)
        {
            var coaName;
            var remarks = "";
            var loop = $('.row-count').val();
            var credit = parseInt($('.credit_'+i).val());

            var totalCredit = parseInt($('.totalCredit').val());

            totalCredit = totalCredit - credit;

            $('#totalCredit').val(totalCredit); 

            $('#itemRow_'+i).remove();

            for (var j = 1; j <= loop; j++)
            {
                coaName = $('.coa_'+j+' option:selected').text();

                if (coaName)
                {
                    if (remarks != "")
                    {
                        remarks += ', ';
                    }
                    
                    remarks += coaName;
                }               
            }

            $('.remarks').val(remarks);
        }
    </script>
@endsection