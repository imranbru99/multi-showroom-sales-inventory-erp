@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label for="showroom">Showrooms</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="showroom" name="showroom">
                        <option value="">Select A Showroom</option>
                        @foreach ($showrooms as $showroom)
                            <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                        @endforeach
                    </select>
                </div>  
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('voucharNo') ? ' has-danger' : '' }}">
                    <label for="vouchar-no">Vouchar No.</label>
                    <input type="text" class="form-control" id="voucharNo" name="voucharNo" value="" placeholder="Enter Vouchar No" required>
                    @if ($errors->has('voucharNo'))
                        @foreach($errors->get('voucharNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
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
                    <table class="table table-bordered table-striped gridTable">
                        <thead>
                            <tr>
                                <th width="10px">Sl</th>
                                <th>Account Name</th>
                                <th width="120px">Debit</th>
                                <th width="120px">Credit</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @php
                                $i =  0;
                            @endphp
                            @foreach ($coas as $coa)
                                @php
                                    $openingBalance = DB::table('tbl_account_transactions')
                                        ->where('voucher_type','OB')
                                        ->where('coa_head_code',$coa->head_code)
                                        ->first();
                                @endphp
                                @if (empty($openingBalance) || $openingBalance->approve == 0)
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <input style="padding-left: 5px;" type="text" class="coa coa_{{ $i }}" id="coa_{{ $i }}" name="coa[]" value="{{ $coa->head_name }}">
                                            <input type="hidden" class="coaId coaId_{{ $i }}" id="coaId_{{ $i }}" name="coaId[]" value="{{ $coa->head_code }}">
                                        </td>

                                        <td>
                                            <input style="text-align: right;" type="number" class="debit debit_{{ $i }}" id="debit_{{ $i }}" name="debit[]" oninput="findTotal()" value="{{ $openingBalance == "" ? 0 : $openingBalance->debit_amount }}">
                                        </td>

                                        <td>
                                            <input style="text-align: right;" type="number" class="credit credit_{{ $i }}" id="credit_{{ $i }}" name="credit[]" oninput="findTotal()" value="{{ $openingBalance == "" ? 0 : $openingBalance->credit_amount }}">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>                                        
                                <td align="center" style="font-weight: bold;" colspan="2">Total Amount</td>
                                <td>
                                    <input type="hidden" class="row-count" value="{{ $i }}">
                                    <input style="text-align: right" type="number" class="totalDebit" id="totalDebit" name="totalDebit" value="0">
                                </td>
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
                url:'{{ route('openingBalanceEntry.getVoucharNo') }}',
                data:{showroomId:showroomId},
                success:function(data){
                    $('#voucharNo').val(data);
                }
            });
        });

        function findTotal()
        {
            var totalDebit = 0;
            var totalCredit = 0;
            var coaName;
            var remarks = "";
            var loop = $('.row-count').val();

            $(".debit").each(function ()
            {
                var debit = parseInt($(this).val());
                totalDebit += isNaN(debit) ? 0 : debit;
            });

            $(".credit").each(function ()
            {
                var credit = parseInt($(this).val());
                totalCredit += isNaN(credit) ? 0 : credit;
            });

            for (var i = 1; i <= loop; i++)
            {
                if ($('.debit_'+i).val() != 0 || $('.credit_'+i).val() != 0)
                {
                    coaName = $('.coa_'+i).val();

                    if (coaName)
                    {
                        if (remarks != "")
                        {
                            remarks += ', ';
                        }
                        
                        remarks += coaName;
                    }
                }               
            }

            if (totalDebit != totalCredit)
            {
                $('.buttonAddEdit').addClass('disabled');
            }
            else
            {
                $('.buttonAddEdit').removeClass('disabled');
            }

            $('.remarks').val(remarks);
            $('#totalDebit').val(totalDebit);
            $('#totalCredit').val(totalCredit);            
        }
    </script>
@endsection