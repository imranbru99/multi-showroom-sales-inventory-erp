@extends('admin.layouts.masterReport')
@section('search_card_body')
    <input type="hidden" value="true" name="searched">
    <div class="row">
        <div class="col-md-6">
            <label for="customer">Customers</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="customer" name="customer[]" multiple>
                    @foreach ($customers as $customerInfo)
                        <option value="{{ $customerInfo->id }}" @if ($customer)  @if (in_array($customerInfo->id, $customer))
                            selected @endif
                    @endif

                    >{{ $customerInfo->name }} ({{ $customerInfo->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label for="amount">Savings Amount</label>
            <select class="form-control chosen-select" id="amount" name="amount">
                    <option value=""  

                    @if ($amount == "")
                        selected
                    @endif

                    >All</option>

                    <option value="0-1000"  

                    @if ($amount == "1-1000")
                        selected
                    @endif

                    >1-1000</option>

                    <option value="1001-5000"  

                    @if ($amount == "1001-5000")
                        selected
                    @endif

                    >1001-5000</option>

                    <option value="5001-10000"  

                    @if ($amount == "5001-10000")
                        selected
                    @endif

                    >5001-10000</option>

                    <option value="10000-999999999999"  

                    @if ($amount == "10000-999999999999")
                        selected
                    @endif

                    >10000+</option>
            </select> 
        </div>
    </div>
@endsection

@section('print_card_header')
    @if ($customer)
        @foreach ($customer as $customerInfo)
            <input type="hidden" name="customer[]" value="{{ $customerInfo }}">
        @endforeach
    @endif
    <input type="hidden" class="form-control" name="amount" value="{{ $amount }}">
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th width="100px">Mobile No</th>
                <th width="100px">Savings</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customerOutstandings as $customerOutstanding)
                <tr>
                    <td>{{ $customerOutstanding['sl'] }}</td>
                    <td>{{ $customerOutstanding['customerAccountCode'] }}</td>
                    <td>{{ $customerOutstanding['customerName'] }}</td>
                    <td>{{ $customerOutstanding['customerMobile'] }}</td>
                    <td>
                        {{ $customerOutstanding['totalCollectionAmount'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
