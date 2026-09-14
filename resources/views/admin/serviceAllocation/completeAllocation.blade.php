@extends('admin.layouts.masterAddEditBlank')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6 text-right">                         
                <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route('serviceAllocation.completeAllocationPrint') }}">
                    <i class="fa fa-print"></i> Print
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped"  name="serviceProductReceive">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Issue Date</th>
                        <th>Finish Date</th>
                        <th>Job No</th>
                        <th>Product Name</th>
                        <th>Product Model</th>
                        <th>Product Serial</th>
                        <th>Dealer</th>
                        <th>Engineer</th>
                        <th>Problem List</th>
                        <th>Product Condition</th>                    
                        <th>Qty</th>
                        <th>Spare Products</th>
                    </tr>
                </thead>
                <tbody id="">
                    @foreach ($completes as $complete)

                    <tr class="row_{{ $complete->id }}">
                        <td></td>
                        <td>{{ date('d-m-Y', strtotime($complete->issue_date)) }}</td>
                        <td>{{ date('d-m-Y', strtotime($complete->finish_date)) }}</td>
                        <td>{{ $complete->invoice_no }}</td>
                        <td>{{ $complete->product->name }}</td>
                        <td>{{ $complete->product_model }}</td>
                        <td>{{ $complete->product_serial }}</td>
                        <td>{{ @$complete->serviceProduct->dealer->name }} ({{ @$complete->serviceProduct->dealer->code }})</td>
                        <td>{{ @$complete->staff->name }} ({{ @$complete->staff->code }})</td>
                        <td>{{ @$complete->serviceProduct->problem_list }}</td>
                        <td>{{ @$complete->serviceProduct->product_condition }}</td>
                        <td>1</td>
                        <td>
                            <table id="dataTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="bg-success text-white">
                                        <th>Product Name</th>
                                        <th>Model No</th>
                                        <th>Serial No</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($complete->spareProducts as $spareProduct)
                                    <tr>
                                        <td>{{ @$spareProduct->product->name }}</td>
                                        <td>{{ $spareProduct->model_no }}</td>
                                        <td>{{ $spareProduct->serial_no }}</td>
                                        <td>1</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-cyan text-white font-weight-bold">
                                        <td colspan="3">Total Qty</td>
                                        <td>{{ count($complete->spareProducts) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
