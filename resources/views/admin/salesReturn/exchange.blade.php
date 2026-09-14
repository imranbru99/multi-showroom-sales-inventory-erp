@extends('admin.layouts.master')

@section('content')
<div class="card">            
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            <div class="col-md-6">  
                <span class="shortlink">
                    <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route('salesReturn.index') }}">
                        <i class="fa fa-edit"></i> Go Back
                    </a>
                </span>                     
            </div>
        </div>

    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="6">Sales Return</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $product = DB::table('tbl_products')
                    ->where('id',$sale->product_id)
                    ->first(); 
                    if(!empty($product)){
                    $productName = $product->name;
                    $productModel = $product->model_no;
                    $cat = DB::table('tbl_categories')
                    ->where('id',$product->category_id)
                    ->first();
                    $catName = $cat->name;
                    }else{
                    $productName = "";
                    $productModel = "";
                    $catName ="";
                    }

                    $productIssue = DB::table('tbl_product_issue')
                    ->where('showroom_id',$showroomId)
                    ->where('id',$sale->issue_id)
                    ->first();


                    if(!empty($productIssue)){
                    $dealerName = DB::table('tbl_dealers')
                    ->where('showroom_id',$showroomId)
                    ->where('id',$productIssue->dealer_id)
                    ->first(); 
                    $dealerName = $dealerName->name;
                    }else{
                    $dealerName = "";
                    }
                    @endphp

                    <tr>
                        <th width="100px">Dealer Name</th>
                        <td width="20px">:</td>
                        <td><?php echo $dealerName; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Date</th>
                        <td width="20px">:</td>
                        <td><?php echo $productIssue->date ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Category Name</th>
                        <td width="20px">:</td>
                        <td><?php echo $catName; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Product Name</th>
                        <td width="20px">:</td>
                        <td><?php echo $productName; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Model No.</th>
                        <td width="20px">:</td>
                        <td><?php echo $productModel; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Serial No.</th>
                        <td width="20px">:</td>
                        <td><?php echo $sale->serial_no; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">commission_rate</th>
                        <td width="20px">:</td>
                        <td><?php echo $sale->commission_rate; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">offer</th>
                        <td width="20px">:</td>
                        <td><?php echo $sale->offer; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Price</th>
                        <td width="20px">:</td>
                        <td><?php echo $sale->price; ?></td>
                    </tr>
                    <tr>
                        <th width="100px">Amount</th>
                        <td width="20px">:</td>
                        <td><?php echo $sale->amount; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer"></div>
</div>
@endsection