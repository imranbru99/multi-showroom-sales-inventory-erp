@extends('admin.layouts.master')

@section('title')
    <title>Admin | {{ $title }}</title>
@endsection

@section('custom-css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-name')
Add Faq
@endsection

@section('content')

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<?php
    use App\Product;
    use App\Customer;
    use App\Checkout;
    use App\Transaction;
    use App\Order;

    $allCustomer = Customer::all();
    $customerCount = $allCustomer->count();

    $topclients = DB::table('customers')
            ->join('transactions', 'customers.id', '=', 'transactions.reference')
            ->select('customers.name',DB::raw('sum(total) as sum'))
            ->groupBy('name')
            ->limit(6)
            ->orderBy('sum','DESC')
            ->get();
          

    $productCount = Product::where('status',1)->count();
    
    $newOrderCount = Checkout::where('status','Waiting')->count();
    $completeOrderCount = Checkout::where('status','Complete')->count();

    $orders = DB::table('checkouts')
            ->join('shippings', 'checkouts.shipping_id', '=', 'shippings.id')
            ->join('transactions', 'transactions.checkout_id', '=', 'checkouts.id')
            ->select('shippings.*','checkouts.*','transactions.total')
            ->orderBy('checkouts.id','DESC')
            ->where("deleted_at",NULL)
            ->get('checkouts');

    $pendingAmount = DB::table('checkouts')
                    ->join('shippings', 'checkouts.shipping_id', '=', 'shippings.id')
                    
                    ->join('transactions', 'checkouts.id', '=', 'transactions.checkout_id')
                    ->where('checkouts.status','Waiting')
                    ->select('transactions.total')
                    ->sum('total');
 

  

    $saleByAmount = DB::table('products')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->select('products.name',DB::raw('sum(orders.price) as sum'))
            ->groupBy('name')
            ->limit(6)
            ->orderBy('sum','DESC')
            ->get();

    $saleByQuantity = DB::table('products')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->select('products.name',DB::raw('sum(orders.qty) as sum'))
            ->groupBy('name')
            ->limit(6)
            ->orderBy('sum','DESC')
            ->get();

    $saleByCategory = DB::table('products')
    ->join('orders', 'products.id', '=', 'orders.product_id')
    ->join('categories', 'products.root_category', '=', 'categories.id')
    ->select('categories.categoryName',DB::raw('sum(orders.price) as sum'))
    ->groupBy('categoryName')
    ->limit(6)
    ->orderBy('sum','DESC')
    ->get();

    $totalsales = Transaction::all();

    $month = date("m");
    $year = date("Y");

    $monthFrom = $year."-".$month."-01";
    $monthTo = $year."-".$month."-31";

    $monthlyIncome =  Transaction::whereBetween('created_at', [$monthFrom, $monthTo])->sum('total');

    $salesbymonth = DB::table('orders')
    ->select('orders.product_id',DB::raw('sum(orders.price) as sum'))
    ->whereBetween('orders.created_at', [$monthFrom, $monthTo])
    ->groupBy('orders.product_id')
    ->orderBy('sum','DESC')
    ->get();

    /*$salesbymonth = DB::table('products')
    ->join('orders', 'products.id', '=', 'orders.product_id')
    ->select('orders.price','orders.created_at','orders.product_id','products.name',DB::raw('sum(orders.price) as sum'))
    ->whereBetween('orders.created_at', [$monthFrom, $monthTo])
    ->groupBy('orders.product_id','orders.created_at','orders.price','products.name')
    ->orderBy('created_at','ASC')
    ->get();*/
?>

<style type="text/css">
    .serial_no{
      width: 4%
    }
    .mobile{
        width: 15%
    }
    .status{
        width: 10%
    }
    .name{
        width: 23%
    }

    .action{
        width: 10%
    }
    .total{
        width: 5%
    }

    table thead{
        line-height: 4px;
    }


</style>

 <div class="container-fluid">
               
                <div style="margin-top: 25px;" class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="icon-screen-desktop"></i></h3>
                                            <p class="text-muted">New Order</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-primary"><a href="{{route('order.new')}}">{{$newOrderCount}}</a></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="icon-note"></i></h3>
                                            <p class="text-muted">Complete Order</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-cyan"><a href="{{route('orderlist.complete')}}">{{$completeOrderCount}}</a></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-cyan" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="icon-doc"></i></h3>
                                            <p class="text-muted">Registered Client</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-purple"><a href="{{route('customers.index')}}">{{$customerCount}}</a> </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-purple" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="icon-bag"></i></h3>
                                            <p class="text-muted">Running Product</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-success"><a href="{{route('product.running')}}">{{$productCount}}</a></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Info box -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Over Visitor, Our income , slaes different and  sales prediction -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card ordertable" style="overflow:scroll; height:441px;">
                            <div class="card-body">
                <h4 class="card-title" style="float: left;">Pending Orders</h4>
                <h4 class="card-title" style="float: right;">Order Amount: {{$pendingAmount}} BDT</h4>
                <div class="table-responsive">
                    <table id="pendingOrderTable" class="table table-bordered table-striped"  name="pendingOrderTable">
                        <thead class="thead">
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Amount</th>
                                <th width="21%">Order status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php
                            foreach ($orders as $pendingOrder) {
                                if ($pendingOrder->status == 'Waiting') {
                                   
                        ?>
                            <tr class="pendingRow_{{ $pendingOrder->id }}">
                              
                                <td>{{$pendingOrder->name}}</td>
                                <td>{{$pendingOrder->mobile}}</td>
                                <td>{{$pendingOrder->total}}</td>
                                <td><span class='badge badge-pill badge-info'>{{$pendingOrder->status}}</span></td>
                                <td class="text-nowrap action">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Order status" data-id="{{ $pendingOrder->id }}"> <i class="fa fa-shopping-bag text-danger m-r-10"></i> 
                                    </a>

                                     <a href="{{route('view.invoices',$pendingOrder->id)}}" title="Order Details"> <i class="fa fa-eye text-success m-r-10"></i> </a>

                                    <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Delete" data-id="{{$pendingOrder->id}}"  data-token="{{ csrf_token() }}"> <i class="fa fa-trash text-danger"></i> </a>
                                </td>
                                
                            </tr>
                        <?php } } ?>
                             
                        </tbody>
                    </table>
                </div>
            </div>
                        </div>
                    </div>
                    <!-- Column -->

                     <div class="col-lg-4 col-md-4">
                        <div class="card ordertable">
                             <div class="card-body" style="min-height: 441px;">
                <h4 class="card-title" style="float: left;">Top Client </h4>
               
                <div class="table-responsive">
                    <table id="checkoutsTable" class="table table-bordered table-striped"  name="checkoutsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php
                            foreach ($topclients as $customer) {
                              
                        ?>
                            <tr>
                                <td>{{$customer->name}}</td>
                                <td>{{$customer->sum}}</td>
                               
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <div class="col-lg-4 col-md-4">
                        <div class="card ordertable">
                           <div class="card-body" style="min-height: 498px;">
                <h4 class="card-title" style="float: left;">Top Sale by Amount </h4>
               
                <div class="table-responsive">
                    <table id="checkoutsTable" class="table table-bordered table-striped"  name="checkoutsTable">
                        <thead>
                            <tr>
                                <th class="name">Name</th>
                                <th class="total">Amount</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php
                            foreach ($saleByAmount as $topSale) {
                             
                        ?>
                            <tr>
                                <td class="name">{{$topSale->name}}</td>
                                <td class="total">{{$topSale->sum}}</td>
                               
                                
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="card ordertable">
                            <div class="card-body" style="min-height: 498px;">
                <h4 class="card-title" style="float: left;">Top Sale by Quantity </h4>
               
                <div class="table-responsive">
                    <table id="checkoutsTable" class="table table-bordered table-striped"  name="checkoutsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="total">Quantity</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody">
                    <?php
                        foreach ($saleByQuantity as $topSale) {
                          
                    ?>
                            <tr>
                                <td>{{$topSale->name}}</td>
                                <td class="total">{{$topSale->sum}}</td>
                               
                                
                            </tr>
                    <?php } ?>

                            
                        </tbody>
                    </table>
                </div>
            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="card ordertable">
                            <div class="card-body" style="min-height: 498px;">
                <h4 class="card-title" style="float: left;">Top Sale by Category </h4>
               
                <div class="table-responsive">
                    <table id="checkoutsTable" class="table table-bordered table-striped"  name="checkoutsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php
                            foreach ($saleByCategory as $categorySale) {
                                
                        ?>
                            <tr>
                                <td>{{$categorySale->categoryName}}</td>
                                <td>{{$categorySale->sum}}</td>
                               
                                
                            </tr>
                        <?php } ?>

                            
                        </tbody>
                    </table>
                </div>
            </div>
                        </div>
                    </div>

                </div>

    <div class="row">
      <div class="col-md-4">
        <div id="bar-chart"></div>
      </div>
      

    </div>

  <div class="row">

          <div class="col-lg-4 col-md-4">
            <div class="card" style="overflow:scroll; height:441px;">
                <div>
                    <div class="d-flex" style="margin-bottom: 10px;">
                        <div style="margin-top: 8px;">
                            <h5 class="card-title" style="margin-left: 10px;"> Sales Overview</h5>
                           
                        </div>
                        <div class="ml-auto" style="margin-top: 3px; margin-right: 7px;">
                            <select class="form-control b-0" id="month">
                                <option <?php if(date('m') == 01){echo "selected";} ?> value="1">January</option>

                                <option <?php if(date('m') == 02){echo "selected";} ?> value="2">February</option>

                                <option <?php if(date('m') == 03){echo "selected";} ?> value="3">March</option>

                                <option <?php if(date('m') == 04){echo "selected";} ?> value="4">April</option>

                                <option<?php if(date('m') == 05){echo "selected";} ?> value="5">May</option>

                                <option <?php if(date('m') == 06){echo "selected";} ?> value="6">Jun</option>

                                <option <?php if(date('m') == 07){echo "selected";} ?> value="7">July</option>

                                <option <?php if(date('m') == 8){echo "selected";} ?> value="8">August</option>

                                <option <?php if(date('m') == 9){echo "selected";} ?>  value="9">September</option>

                                <option <?php if(date('m') == 10){echo "selected";} ?> value="10">October</option>

                                <option <?php if(date('m') == 11){echo "selected";} ?> value="11">November</option>

                                <option <?php if(date('m') == 12){echo "selected";} ?> value="12">December</option>
                            </select>
                        </div>
                    </div>
                </div>
               
                <div class=" bg-light">
                    <div class="row">
                        <div class="col-6">
                            <h4 style="margin-left: 10px;"><span class="monthName"><?php echo date('F') ?></span> <?php echo date('Y'); ?></h4 style="margin-left: 10px;">
                        </div>
                        <div class="col-6 align-self-center display-6 text-right">
                            <h4 class="text-success monthlyIncome" style="margin-right: 8px;">{{$monthlyIncome}} BDT</h4></div>
                    </div>
                </div>
                <div class="">
                    <table class="table table-hover no-wrap">
                        <thead>
                            <tr>
                                <th width="71%">NAME</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="monthly-sales">
                <?php foreach ($salesbymonth as $monthlySale) {
                    $products = Product::where('id',$monthlySale->product_id)->first();
                  
                 ?>
                            <tr>
                                <td class="txt-oflo" width="71%">{{$products->name}}</td>
                                <td><span class="text-success">{{$monthlySale->sum}} BDT</span></td>
                                
                            </tr>
                 <?php } ?>          
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column -->
        <div class="col-lg-8 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex m-b-40 align-items-center no-block">
                        <h5 class="card-title ">Monthly SALES Flow</h5>
                       
                    </div>
                    <div id="monthly-chart" style="height: 340px;"></div>
                </div>
            </div>
        </div>

    </div>

                <div class="row" style="display: none;">

                    <!-- Column -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex m-b-40 align-items-center no-block">
                                    <h5 class="card-title ">YEARLY SALES Flow</h5>
                                    <div class="ml-auto">
                                        <ul class="list-inline font-12">
                                            <li><i class="fa fa-circle text-cyan"></i> Iphone</li>
                                            <li><i class="fa fa-circle text-primary"></i> Ipad</li>
                                            <li><i class="fa fa-circle text-purple"></i> Ipod</li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="morris-area-chartdd" style="height: 340px;"></div>
                            </div>
                        </div>
                    </div>

                     <div class="col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex m-b-40 align-items-center no-block">
                                    <h5 class="card-title ">Yearly Sales Over View</h5>
                                    <div class="ml-auto">
                                       
                                    </div>
                                </div>
                                <div id="morris-area-chartsdzfc" style="height: 340px;"></div>
                            </div>
                        </div>
                    </div>
                   
                </div>
               </div>

               <div id="showShipping" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Order Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="container " id="shippingContent">
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection

@section('custom-js')

    <!-- This is data table -->

    <script src="{{ asset('/public/admin-elite/assets/node_modules/datatables/jquery.dataTables.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            var updateThis ;

            // Switchery
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            $('.js-switch').each(function() {
                new Switchery($(this)[0], $(this).data());
            });

            
           

            //ajax
            //ajax show code
            $('#pendingOrderTable tbody').on( 'click', 'i.fa-eye', function () { 
                updateThis = this;
                checkout_id = $(this).parent().data('id');
                showFunction(checkout_id);
                            
            });

         
            //ajax delete code
            $('#pendingOrderTable tbody').on( 'click', 'i.fa-trash', function () {
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                checkout_id = $(this).parent().data('id');
                var checkout = this;
                swal({   
                    title: "Are you sure?",   
                    text: "You will not be able to recover this imaginary file!",   
                    type: "warning",   
                    showCancelButton: true,   
                    confirmButtonColor: "#DD6B55",   
                    confirmButtonText: "Yes, delete it!",   
                    cancelButtonText: "No, cancel plx!",   
                    closeOnConfirm: false,   
                    closeOnCancel: false 
                }, function(isConfirm){   
                    if (isConfirm) {     
                        $.ajax({
                            type: "POST",                           
                            url: "{{ route('order.delete') }}",
                           
                            data: {
                                checkout_id:checkout_id
                            },
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Order information deleted successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $(".pendingRow_"+checkout_id).remove();
                            },
                            error: function(response) {
                                error = "Failed.";
                                swal({
                                    title: "<small class='text-danger'>Error!</small>", 
                                    type: "error",
                                    text: error,
                                    timer: 1000,
                                    html: true,
                                });
                            }
                        });   
                    } else { 
                        swal({
                            title: "Cancelled", 
                            type: "error",
                            text: "Your order list is safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            }); 

    
            //ajax show status code
            $('#pendingOrderTable tbody').on( 'click', 'i.fa-shopping-bag', function () { 
                checkout_id = $(this).parent().data('id');
                                $.ajax({
                    type: "GET",
                    url: "{{ route('checkouts.index') }}" + "/" + checkout_id + "/edit",
                    data: "checkout_id=" + checkout_id + "&option=status" ,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(response) {      
                        showStatus(response);

                    },
                    error: function(response) {
                        error = "Something wrong.";
                        swal({
                            title: "<small class='text-danger'>Error!</small>", 
                            type: "error",
                            text: error,
                            timer: 1000,
                            html: true,
                        });
                    }
                });              
            });

            function showStatus(response){
                
                <?php $checkoutStatus = ['Waiting', 'Processing', 'Shipping', 'Complete'] ?>

                if(response.checkout.status == 'Waiting') 
                    badge = '"badge badge-pill badge-info"';
                else if(response.checkout.status == 'Processing') 
                    badge = '"badge badge-pill badge-danger"';  
                else if(response.checkout.status == 'Shipping') 
                    badge = '"badge badge-pill badge-warning"'; 
                else if(response.checkout.status == 'Complete') 
                    badge = '"badge badge-pill badge-success"';
                else
                    badge = '"badge badge-pill badge-danger"';

                var content =   `<div class="form-group text-center">
                                    <h4><span class=`+badge+`
                                    style="float: center;"> `+response.checkout.status+`</span></h4>
                                </div>`+
                                `<div class="row col-sm-12">
                                Total order status
                                </div>
                                <form action="javascript:void(0)" method="POST" name="checkoutForm">
                                    <div class="form-group row">
                                    <input type="hidden" name="checkout_id" value="`+response.checkout.id+`">
                                        <div class="col-sm-6 ">
                                            <select class="form-control" name="status">
                                                <option value="">--- Select status ---</option>
                                                @foreach($checkoutStatus as $key=>$value)
                                                    <option value="{{ $value }}" >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6"><button type="button" class="btn btn-info waves-effect" onclick="checkoutFunction(this)">Update status</button> 
                                        </div>
                                    </div>
                                </form>`
                               
                                ;
                $('#shippingContent').html(content);
                $("#showShipping").modal();

                document.forms['checkoutForm'].elements['status'].value = response.checkout.status;

            }

    
        });

        function checkoutFunction(){
            checkout_id = $("form[name='checkoutForm'] input[name='checkout_id']").val();
            status = $("form[name='checkoutForm'] select[name='status']").val();
            statusChange(checkout_id, 'checkout', status);

        }

        function statusChange(checkout_id, option, status) {
            $.ajax({
                type: "GET",
                url: "{{ route('checkouts.index') }}" + "/" + checkout_id + "/status",
                data: "checkout_id=" + checkout_id + "&option=" +option+ "&status=" +status  ,
                cache:false,
                contentType: false,
                processData: false,
                success: function(response) {  
                    checkout = response.checkout
                    $('.modal').modal('hide');
                    if(status != 'Waiting'){
                        $(".pendingRow_"+checkout_id).remove();
                    }
                    swal({
                        title: "<small class='text-success'>Success!</small>", 
                        type: "success",
                        text: "Checkout successfully updated!",
                        timer: 2000,
                        html: true,
                    });
                    
                },
                error: function(response) {
                    error = "Something wrong.";
                    swal({
                        title: "<small class='text-danger'>Error!</small>", 
                        type: "error",
                        text: error,
                        timer: 1000,
                        html: true,
                    });
                }
            });
        }
    </script>


<?php
    $dataPoints = array();
    $i = 0;
    foreach ($totalsales as $monthlySales) {
         array_push($dataPoints, array("y"=>$monthlySales->total, "label"=> $i));
         
        }
            
   /* $dataPoints = array(
        array("y" => 25, "label" => "5"),
        array("y" => 15, "label" => "10"),
        array("y" => 25, "label" => "15"),
        array("y" => 5, "label" => "20"),
        array("y" => 10, "label" => "25"),
        array("y" => 0, "label" => "30"),
    );*/
 
?>

   <script>
        window.onload = function () {
         
        var chart = new CanvasJS.Chart("monthly-chart", {
            title: {
                text: ""
            },
            axisY: {
                title: ""
            },
            data: [{
                type: "line",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();
         
        }
</script>


<script type="text/javascript">
   $(document).ready(function(){ 
    $("#month").change(function(){ 
      var month = $(this).val(); 
      $.ajax({ 
        type: "GET",
        url: "<?php echo url('/admin/monthly-sales');?>"+"/"+ month, 
        data: {}, 
        success: function(response){ 
       
          $("#monthly-sales").html(response.monthlySales);
          $(".monthlyIncome").html(response.monthlyIncome + " BDT"); 
          $(".monthName").html(response.monthName);
        }
      });

    });
  });
</script>


<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


@endsection



