    @extends('admin.layouts.masterReport')


    @section('search_card_body')
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="print" value="print">
            </div>
        </div>

        
        <div class="row">

            <div class="col-md-8">
              
                <label for="search-type">Search Showroom Project</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="dealers" name="dealers" >
                       
                        @foreach($allShowroomProject as $singleShowroomProject)
                            <option value="{{$singleShowroomProject->id}}">{{$singleShowroomProject->name}}</option>
                        @endforeach
                      
                    </select>
                </div>  
       
            </div>
        </div>

    @endsection

    @section('print_card_header')
        <input type="hidden" name="fromDate" value="{{ $fromDateToform }}">
        <input type="hidden" name="toDate" value="{{ $toDateToform }}">

        @if ($searchType)
        @foreach ($searchType as $searchKey)
        <input type="hidden" name="searchType[]" value="{{ $searchKey }}">
        @endforeach
        @endif
        <input type="hidden" name="searchText" value="{{ $searchText }}">
        <input type="hidden" id="print_value" name="print" value="{{ $print }}">
    @endsection



    @section('print_card_body')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th rowspan="2" >Sl</th>
                    <th rowspan="2" >A/C No</th>
                    <th rowspan="2">Customer Name</th>
                    <th rowspan="2">Mobile No</th>

                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->total_sales_price ?? ""}} BDT</th>


                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->total_discount ?? ""}} BDT</th>

                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->gift_voucher?? ""}} BDT</th>

                   <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->exchange_crt ?? ""}} BDT</th>

                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->total_collection ?? ""}} BDT</th>
                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->total_balance ?? ""}} BDT</th>
                    <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{ $data->total_agreement_tk ?? ""}} BDT</th>
                    
                </tr>
                <tr>
                    <th>Sales</th>
                    <th>Discount</th>
                    <th>Gift Voucher</th>
                    <th>Exchange Crt</th>
                    <th>Collection</th>
                    <th>Balance</th>
                    <th>Agreement</th>
                    
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $balance  = 0;
                  
                @endphp

                @foreach ($retailSalesReports as $retailSalesReport)

                {{--  @php
                    $balance = $retailSalesReport->sales - $retailSalesReport->collection;
                @endphp --}}
                    <tr>
                        <td>{{ $sl++ }}</td>
                            <td>{{ $retailSalesReport->account_no}}</td>
                        <td>{{ $retailSalesReport->name }}</td>
                        <td>{{ $retailSalesReport->mobile_no}}</td>
                        <td>{{  round($retailSalesReport->sales, 2) }}</td>
                        <td>{{  round($retailSalesReport->discount, 2) }}</td>
                        <td>{{  round($retailSalesReport->gift_voucher, 2) }}</td>
                        <td>{{  round($retailSalesReport->exchange_crt, 2)}}</td>
                        <td>{{  round( $retailSalesReport->collection, 2) }}</td>
                        <td>{{  round($retailSalesReport->sales - $retailSalesReport->discount - $retailSalesReport->gift_voucher - $retailSalesReport->exchange_crt - $retailSalesReport->collection, 2)}}</td>
                        <td>{{ round($retailSalesReport->agreement_tk, 2)}}</td>
                        
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endsection