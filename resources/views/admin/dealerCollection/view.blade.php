@extends('admin.layouts.master')

@section('content')
    @php
        use App\Product;
        use App\CustomerRegistrationSetup;
        use App\ShowroomSetup;
    @endphp
    <style type="text/css">
        .blockTitle span{
            font-weight: bold;
        }
    </style>
    <div style="padding-bottom: 10px;"></div>

    @php
        $message = Session::get('msg');
    @endphp

    @if (isset($message))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> {{ $message }}
        </div>
    @endif

    @php
        Session::forget('msg');
    @endphp

    @if( count($errors) > 0 )
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Oops!</strong> {{ $errors->first() }}
        </div>
    @endif
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{@$title}}</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                        <a target="_blank" class="btn btn-outline-info btn-lg" href="{{ route('invoiceSetup.printInvoice',$invoice->id) }}">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>

                         <a target="_blank" class="btn btn-outline-info btn-lg" href="{{ route('invoiceSetup.printChalan',$invoice->id) }}">
                            <i class="fa fa-print"></i> Print Chalan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body" style="padding-bottom: 50px;">
                @php
                    /*$customer = CustomerRegistrationSetup::where('id',$invoice->customer_id)->first();
                    $showRoom = ShowroomSetup::where('id',$getCustomerProduct->showroom_id)->first();*/
                    $invoiceDate = date('d-m-Y',strtotime($invoice->created_at));
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="blockTitle"><span>Invoice No</span> #{{@$invoice->invoice_no}}</h5>
                        <h5 class="blockTitle"><span>Invoice Date :</span> {{@$invoiceDate}}</h5>
                    </div>

                    <div class="col-md-6" style="text-align: right;">
                        <h5 class="blockTitle" style="border-bottom: 1px solid #333;display: inline-block;padding-bottom: 5px;"><span>Invoice To</span></h5>
                        <h4>{{ $customer->name}}</h4>
                        <h4>{{ $customer->phone_no}}</h4>
                        <h4>{{$invoice->customer_product_usage_address }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="190px">Showroom</th>
                                    <th>Code</th>
                                    <th width="220px">Name</th>
                                    <th>Price</th>
                                    <th width="112px">Model</th>
                                    <th>Color</th>
                                    <th width="60px">Warranty</th>
                                    <th style="text-align: center;" width="115px">Purchase Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{{$showRoom->name}}</td>
                                    <td>{{$productInfo->code}}</td>
                                    <td>{{$productInfo->name}}</td>
                                    <td>{{$invoice->customer_product_price}}</td>
                                    <td>{{$invoice->customer_product_model}}</td>
                                    <td>{{$invoice->customer_product_color}}</td>
                                    <td align="center">{{$invoice->customer_product_waranty}}</td>
                                    <td align="center">{{$invoice->customer_product_purchase_date}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
 
@endsection
