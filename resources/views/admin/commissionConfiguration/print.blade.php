@extends('admin.layouts.masterPrint')

@section('custome-css')
  
@endsection

@section('content')
    @php
        use App\DealerSetup;
        use App\StaffSetup;
        use App\Product;
        use App\CommissionConfiguration;
        $commissionType = array('Dealer Commission' => 'Dealer Commission', 'Staff Commission' => 'Staff Commission', 'Group Commission' => 'Group Commission');

            $dealer = DealerSetup::where('id',@$commission->dealer_id)->first();
            $staff = StaffSetup::where('id',@$commission->staff_id)->first();
            if(@$dealer){
                $name = $dealer->name;
            }elseif(@$staff){
                $name = $staff->name;
            }else{

            }
    @endphp
    <table id="report-header">
        <tr>
            <td>Commission Details</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table width="100%">
        <tr>
            <td width="50%">
                <table>
                    <tbody>
                        <tr>
                            <td>Type</td>
                            <td width="10px">:</td>
                            <td>{{$commission->commission_type}}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="50%" align="right">
                <table>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td width="10px">:</td>
                            <td>{{$name}}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        
    </table>


    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th>Product Category Name</th>
                <th width="150px">Commission Rate (%)</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                if ($commission->dealer_id == "")
                {
                    $commissionRates = CommissionConfiguration::where('staff_id',$commission->staff_id)->get();
                }

                if ($commission->staff_id == "")
                {
                    $commissionRates = CommissionConfiguration::where('dealer_id',$commission->dealer_id)->get();
                }
            @endphp
            @foreach ($categoryList as $category)
                @foreach ($commissionRates as $commissionRate)
                    @if ($commissionRate->category_id == $category->id)
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $category->name }}</td>
                            <td style="text-align: center;">{{ $commissionRate->commission_rate }}</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection