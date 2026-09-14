@extends('admin.layouts.master')

@section('content')
    @php
        use App\DealerSetup;
        use App\StaffSetup;
        use App\Product;
        use App\CommissionConfiguration;
        $dealer = DealerSetup::where('id',@$commission->dealer_id)->first();
        $staff = StaffSetup::where('id',@$commission->staff_id)->first();
        if(@$dealer)
        {
            $name = $dealer->name;
        }
        elseif(@$staff)
        {
            $name = $staff->name;
        }
    @endphp
    <style type="text/css">
        .gridTable{
           padding-left: 5px !important;
        }
    </style>
    <div style="padding-bottom: 10px;"></div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer-product-name">Commission Type</label>
                    <div class="form-group" id="prodct-select-menu">
                        <input type="text" name="commissionType" value="{{$commission->commission_type}}" class="form-control" readonly="">
                    </div>
                </div>

                <div class="col-md-6 defaultBox">
                    <label for="customer-product-name">Name</label>
                    <div class="form-group" id="prodct-select-menu">
                        <input type="text" value="{{$name}}" class="form-control" readonly="">
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <thead style="background-color: #00c292;">
                            <tr>
                                <th width="20px">SL</th>
                                <th>Product Category Name</th>
                                <th width="150px">Commission Rate (%)</th>
                            </tr>
                        </thead>
                        <tbody id="">
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
                                @php
                                    $product = Product::where('category_id',$category->id)->get();
                                @endphp
                                @if (count($product) > 0)
                                    @foreach ($commissionRates as $commissionRate)
                                        @if ($commissionRate->category_id == $category->id)
                                            <tr>
                                                <td>{{ $sl++ }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td style="text-align: center;">{{ $commissionRate->commission_rate }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

