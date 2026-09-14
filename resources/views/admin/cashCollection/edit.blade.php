@extends('admin.layouts.masterAddEdit')

@section('card_body')
@php
use App\CashCollection;
$collectionDate = date('d-m-Y',strtotime($cashCollection->collection_date));
@endphp
    <div class="card-body">
        <input type="hidden" name="collectionId" value="{{$cashCollection->id}}">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('invoiceId') ? ' has-danger' : '' }}">
                            <label for="invoice-id">Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control" value="{{$invoice->invoice_no}}" readonly="">
                            @if ($errors->has('invoiceId'))
                                @foreach($errors->get('invoiceId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('collectionNo') ? ' has-danger' : '' }}">
                            <label for="collection-no">Collection No</label>
                            <input type="text" name="collectionNo" class="form-control" value="{{@$cashCollection->collection_no}}" readonly>
                            @if ($errors->has('collectionNo'))
                                @foreach($errors->get('collectionNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Collector</label>
                        <div class="form-group {{ $errors->has('collector') ? ' has-danger' : '' }}">
                            <input type="text" name="collector" value="{{ $cashCollection->collectorName }}" class="form-control collector" readonly="">
                            @if ($errors->has('collector'))
                                @foreach($errors->get('collector') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('productCode') ? ' has-danger' : '' }}">
                            <label for="product_code">Product Code</label>
                            <input type="text" class="form-control productCode" id="productCode" name="productCode" value="{{ $product->code }}" required readonly="">
                            @if ($errors->has('productCode'))
                                @foreach($errors->get('productCode') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
                            <label for="product-model">Product Model</label>
                            <input type="text" class="form-control productModel" id="productModel" name="productModel" value="{{ $product->model_no }}" required readonly="">
                            @if ($errors->has('productModel'))
                                @foreach($errors->get('productModel') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('productName') ? ' has-danger' : '' }}">
                    <label for="product-name">Product Name</label>
                    <input type="text" class="form-control productName" id="productName" name="productName" value="{{ $product->name }}" required readonly="">
                    @if ($errors->has('productName'))
                        @foreach($errors->get('productName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('productColor') ? ' has-danger' : '' }}">
                            <label for="product-color">Product Color</label>
                            <input type="text" class="form-control productColor" id="productColor" name="productColor" value="{{ $product->color }}" required readonly="">
                            @if ($errors->has('productColor'))
                                @foreach($errors->get('productColor') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('productWaranty') ? ' has-danger' : '' }}">
                            <label for="product-warranty">Product Warranty</label>
                            <input type="text" class="form-control productWaranty" id="productWaranty" name="productWaranty" value="{{ $product->warranty }}" required readonly="">
                            @if ($errors->has('productWaranty'))
                                @foreach($errors->get('productWaranty') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('collectionDate') ? ' has-danger' : '' }}">
                            <label for="collection-date">Collection Date</label>
                            <input type="text" name="collectionDate" value="{{@$collectionDate}}" class="form-control datepicker" readonly="">
                            @if ($errors->has('collectionDate'))
                                @foreach($errors->get('collectionDate') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('invoiceAmount') ? ' has-danger' : '' }}">
                            <label for="invoice-amount">Invoice Amount</label>
                            <input type="text" class="form-control invoiceAmount" id="invoiceAmount" name="invoiceAmount" value="{{@$cashCollection->invoice_amount}}" required readonly="">
                            @if ($errors->has('invoiceAmount'))invoiceAmount
                                @foreach($errors->get('invoiceAmount') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('previousCollection') ? ' has-danger' : '' }}">
                            <label for="previous-collection">Previous Collection</label>
                            <input type="number" name="previousCollection" class="form-control previousCollection" id="previousCollection" value="{{@$cashCollection->previous_collection}}" readonly="">
                            @if ($errors->has('previousCollection'))
                                @foreach($errors->get('previousCollection') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('collectionAmount') ? ' has-danger' : '' }}">
                            <label for="collection-amount">Collection Amount</label>
                            <input type="number" name="collectionAmount" class="form-control collectionAmount" id="collectionAmount" value="{{$cashCollection->collection_amount}}" required="" oninput="CalculateDue()">
                            @if ($errors->has('collectionAmount'))
                                @foreach($errors->get('collectionAmount') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('currentDue') ? ' has-danger' : '' }}">
                            <label for="curent-due">Current Due</label>
                            <input type="number" name="currentDue" class="form-control currentDue" value="{{$cashCollection->current_due}}" readonly="">
                            @if ($errors->has('currentDue'))
                                @foreach($errors->get('currentDue') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <label for="remarks">Remarks</label>
                    <textarea name="remarks" rows="5" class="form-control">{{$cashCollection->remarks}}</textarea>
                    @if ($errors->has('remarks'))
                        @foreach($errors->get('remarks') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
        
@endsection

@section('custom-js')
    <script type="text/javascript">
        /*javascript code for invoice information*/

            $(document).on('change', '.invoiceNo', function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                var invoiceId = $('.invoiceNo option:selected').val();
                if(invoiceId != ''){
                    $.ajax({
                        type:'post',
                        url:'{{ route('cashCollection.getInvoiceInformation') }}',
                        data:{invoiceId:invoiceId},
                        success:function(data){
                            var invoice = data.invoice;
                            var collection_amount = invoice.customer_product_price-data.previous_collection;
                            //var collection_amount = data.invoiceAmount-data.collections;
                            $('.invoiceAmount').val(parseFloat(invoice.customer_product_price).toFixed(2));
                            $('.previousCollection').val(parseFloat(data.previous_collection).toFixed(2));
                            $('.collectionAmount').val(parseFloat(collection_amount).toFixed(2));
                            $('.currentDue').val(0.00);
                        }
                    });
                }else{
                   $('.productModel').val('');
                   $('.cashPrice').val(''); 
                }
            });

            function CalculateDue(){
                var collectionAmount = document.getElementById('collectionAmount').value;
                if (document.getElementById('previousCollection').value) {
                    var previousCollection = document.getElementById('previousCollection').value;
                }else{
                    var previousCollection = 0;
                }
                 var invoiceAmount = document.getElementById('invoiceAmount').value;
                 var newAmount = parseInt(collectionAmount) + parseInt(previousCollection);
                 var currentDue = parseInt(invoiceAmount) - parseInt(newAmount);
                $('.currentDue').val(currentDue.toFixed(2));

                if (currentDue < 0) {
                    alert('Collection amount sholuld not be cross invoice amount!');
                }
            }

        /*end code for product info*/

        $("form").submit(function(e){
            if ($('.currentDue').val() < 0) {
                    alert('Collection amount sholuld not be cross invoice amount!');
                     e.preventDefault();
                }
          });

        </script>
@endsection
