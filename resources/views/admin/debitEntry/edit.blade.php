@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label for="showroom">Showrooms</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="showroomName" name="showroomName" value="{{ $debitEntry->showroomName }}" readonly>
                    <input type="hidden" class="form-control" id="showroom" name="showroom" value="{{ $debitEntry->showroom_id }}" readonly>
                    <input type="hidden" class="form-control" id="createdBy" name="createdBy" value="{{ $debitEntry->created_by }}" readonly>
                </div>  
            </div>

            <div class="col-md-4">
                <label for="credit-account-head">Credit Account Head</label>
                <div class="form-group">
                    <select class="chosen-select" id="creditAccountHead" name="creditAccountHead">
                        <option value="">Select Account Name</option>
                        @foreach ($creditCoas as $creditCoa)
                        	@php
                        		if ($creditCoa->head_code == $debitEntry->coa_head_code)
                        		{
                        			$select = "selected";
                        		}
                        		else
                        		{
                        			$select = "";
                        		}                        		
                        	@endphp
                            <option value="{{ $creditCoa->head_code }}" {{ $select }}>{{ $creditCoa->head_name }}</option>
                        @endforeach
                    </select>
                </div>  
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('voucharNo') ? ' has-danger' : '' }}">
                    <label for="vouchar-no">Vouchar No.</label>
                    <input type="text" class="form-control" id="voucharNo" name="voucharNo" value="{{ $debitEntry->voucher_no }}" readonly>
                    @if ($errors->has('voucharNo'))
                        @foreach($errors->get('voucharNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="transaction-date">Transaction Date</label>
                    <input type="text" class="form-control add_datepicker" name="transactionDate" value="{{ $debitEntry->voucher_date }}" placeholder="Select Transaction Date" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control remarks" id="remarks" name="remarks" rows="2">{{ $debitEntry->narration }}</textarea>
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
                                <th width="120px">Debit</th>
                                <th width="90px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        	@php
                        		$i = 0;
                        	@endphp

                        	@foreach ($debitEntries as $debitEntryInfo)
                        		@php
                        			$i++;
                        		@endphp
	                            <tr>
	                                <td>
	                                    <select class="chosen-select coa coa_{{ $i }}" id="coa" name="coa[]">
	                                        <option value="">Select Account Name</option>
	                                        @foreach ($coas as $coa)
					                        	@php
					                        		if ($coa->head_code == $debitEntryInfo->coa_head_code)
					                        		{
					                        			$select = "selected";
					                        		}
					                        		else
					                        		{
					                        			$select = "";
					                        		}                        		
					                        	@endphp
	                                            <option value="{{ $coa->head_code }}" {{ $select }}>{{ $coa->head_name }}</option>
	                                        @endforeach
	                                    </select>
	                                </td>

	                                <td>
	                                    <input style="text-align: right;" type="number" class="debit debit_{{ $i }}" id="debit" name="debit[]" oninput="findTotal()" value="{{ $debitEntryInfo->debit_amount }}">
	                                </td>

	                                <td align="center">
	                                    <span class="btn btn-outline-success btn-sm add-item" onclick="addItem()" style="width: 100%;">
	                                        <i class="fa fa-plus-circle"></i>
	                                    </span>
	                                </td>
	                            </tr>
                        	@endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td align="center" style="font-weight: bold;">Total Amount</td>
                                <td>
                                	<input type="hidden" class="row-count" value="{{ $i }}">
                                	<input style="text-align: right" type="number" class="totalDebit" id="totalDebit" name="totalDebit" value="{{ $debitEntry->credit_amount }}" readonly>
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
                url:'{{ route('debitEntry.getVoucharNo') }}',
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
                        '<input style="text-align: right;" type="number" class="debit debit_'+total+'" id="debit" name="debit[]" oninput="findTotal()" value="0">'+
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
                url: "{{ route('debitEntry.getCoa') }}",
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
            var totalDebit = 0;
            var coaName;
            var remarks = "";
            var loop = $('.row-count').val();

            $(".debit").each(function ()
            {
                var debit = parseInt($(this).val());
                totalDebit += isNaN(debit) ? 0 : debit;
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
            $('#totalDebit').val(totalDebit);           
        }

        function removeItem(i)
        {
            var coaName;
            var remarks = "";
            var loop = $('.row-count').val();
            var debit = parseInt($('.debit_'+i).val());
            var credit = parseInt($('.credit_'+i).val());

            var totalDebit = parseInt($('.totalDebit').val());

            totalDebit = totalDebit - debit;

            $('#totalDebit').val(totalDebit); 

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