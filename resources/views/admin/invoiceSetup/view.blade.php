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
                        <a target="_blank" class="btn btn-outline-info btn-lg" href="{{ route('invoiceSetup.printInvoice',$totalInvoice->id) }}">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>

                         <a target="_blank" class="btn btn-outline-info btn-lg" href="{{ route('invoiceSetup.printChalan',$totalInvoice->id) }}">
                            <i class="fa fa-print"></i> Print Chalan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body" style="padding-bottom: 50px;">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="blockTitle"><span>Invoice No : </span> #{{@$totalInvoice->invoice_no}}</h5>
                        <h5 class="blockTitle"><span>Invoice Date :</span> {{ date('d-m-Y', strtotime($totalInvoice->invoice_date)) }}</h5>
                        <h5 class="blockTitle"><span>Company Name :</span> {{ $totalInvoice->showRoomName }}</h5>
                    </div>

                    <div class="col-md-6" style="text-align: right;">
                        <h5 class="blockTitle" style="border-bottom: 1px solid #333;display: inline-block; padding-bottom: 5px;"><span>Invoice To</span></h5>
                        <h4>{{ $totalInvoice->customerName}}</h4>
                        <h4>{{ $totalInvoice->customerPhone}}</h4>
                        <h4>{{$totalInvoice->customerPresentAddress }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" style="text-align: center;" width="100px">Purchase Date</th>
                                    <th class="text-center" width="100px">Code</th>
                                    <th class="text-center" width="220px">Name</th>
                                    <th class="text-center" width="150px">Model</th>
                                    <th class="text-center" width="100px">Color</th>
                                    <th class="text-center" width="60px">Warranty</th>
                                    <th class="text-center" width="60px">Price</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td align="center">{{ date('d-m-Y', strtotime($invoice->customer_product_purchase_date)) }}</td>
                                        <td>{{ $invoice->productCode }}</td>
                                        <td>{{ $invoice->productName }}</td>
                                        <td>{{ $invoice->customer_product_model }}</td>
                                        <td>{{ $invoice->customer_product_color }}</td>
                                        <td align="center">{{ $invoice->customer_product_waranty }}</td>
                                        <td align="right">{{ $invoice->customer_product_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="6" align="right"><h5 class="blockTitle"><span>Total</span></h5></td>
                                    <td align="right">{{ $totalInvoice->total_customer_product_price }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
 
@endsection
