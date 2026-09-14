@extends('admin.layouts.masterAddEdit')

@section('card_body')
    @php
        use App\CashCollection;
        $lastCashCollection = CashCollection::max('id');
        if(@$lastCashCollection)
        {
            $lastCashCollectionId = $lastCashCollection + 1;
        }
        else
        {
            $lastCashCollectionId = 1;
        }
        $randomNumber = 10000+$lastCashCollectionId;
        $collectionNo = "col-".$lastCashCollectionId."-".date('y')."-".$randomNumber;
    @endphp
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('invoiceId') ? ' has-danger' : '' }}">
                            <label for="invoice-id">Invoice No</label>
                            <select class="form-control chosen-select invoiceNo" name="invoiceId" data-placeholder="Select invoice" required="">
                                <option value="">Select Invoice No</option>
                                @foreach ($invoiceList as $invoice)
                                    <option value="{{$invoice->id}}">{{$invoice->invoice_no}}</option>
                                @endforeach
                            </select>
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
                            <input type="text" name="collectionNo" class="form-control" value="{{@$collectionNo}}" readonly>
                            @if ($errors->has('collectionNo'))
                                @foreach($errors->get('collectionNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Collector</label>
                        <div class="form-group {{ $errors->has('collectorId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select" name="collectorId" required="">
                                <option value="">Select Collector</option>
                                @foreach ($collectorList as $collector)
                                    <option value="{{$collector->id}}">{{$collector->name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('collectorId'))
                                @foreach($errors->get('collectorId') as $error)
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
                            <input type="text" class="form-control productCode" id="productCode" name="productCode" value="{{ old('productCode') }}" required readonly="">
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
                            <input type="text" class="form-control productModel" id="productModel" name="productModel" value="{{ old('productModel') }}" required readonly="">
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
                    <input type="text" class="form-control productName" id="productName" name="productName" value="{{ old('productName') }}" required readonly="">
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
                            <input type="text" class="form-control productColor" id="productColor" name="productColor" value="{{ old('productColor') }}" required readonly="">
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
                            <input type="text" class="form-control productWaranty" id="productWaranty" name="productWaranty" value="{{ old('productWaranty') }}" required readonly="">
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
                            <input type="text" name="collectionDate" class="form-control add_datepicker" readonly="">
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
                            <input type="text" class="form-control invoiceAmount" id="invoiceAmount" name="invoiceAmount" value="{{ old('invoiceAmount') }}" required readonly="">
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
                            <input type="number" name="previousCollection" class="form-control previousCollection" id="previousCollection" readonly="">
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
                            <input type="number" name="collectionAmount" class="form-control collectionAmount" id="collectionAmount" required="" oninput="CalculateDue()">
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
                            <input type="number" name="currentDue" class="form-control currentDue" readonly="">
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
                    <textarea name="remarks" rows="5" class="form-control"></textarea>
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
                            var product = data.product;
                            var collection_amount = invoice.customer_product_price-data.previous_collection;

                            $('.productCode').val(product.code);
                            $('.productModel').val(product.model_no);
                            $('.productName').val(product.name);
                            $('.productColor').val(product.color);
                            $('.productWaranty').val(product.warranty);
                            $('.invoiceAmount').val(parseFloat(invoice.customer_product_price).toFixed(2));
                            $('.previousCollection').val(parseFloat(data.previous_collection).toFixed(2));
                            $('.collectionAmount').val(parseFloat(collection_amount).toFixed(2));
                            $('.currentDue').val(0.00);
                        }
                    });
                }else{
                    $('.productCode').val('');
                    $('.productModel').val('');
                    $('.productName').val('');
                    $('.productColor').val('');
                    $('.productWaranty').val('');
                    $('.invoiceAmount').val('');
                    $('.previousCollection').val(''); 
                    $('.collectionAmount').val(''); 
                    $('.currentDue').val(''); 
                }
            });

            function CalculateDue(){
                var collectionAmount = document.getElementById('collectionAmount').value;
                if (document.getElementById('previousCollection').value)
                {
                    var previousCollection = document.getElementById('previousCollection').value;
                }
                else
                {
                    var previousCollection = 0;
                }

                var invoiceAmount = document.getElementById('invoiceAmount').value;
                var previousCollection = parseInt($('.previousCollection').val());

                if (collectionAmount <= (invoiceAmount - previousCollection))
                {
                    var newAmount = parseInt(collectionAmount) + parseInt(previousCollection);
                    var currentDue = parseInt(invoiceAmount) - parseInt(newAmount);
                    $('.currentDue').val(currentDue.toFixed(2));
                }
                else
                {
                    swal('Invalid Amount!','','warning');
                    $('.collectionAmount').val(parseInt(invoiceAmount - previousCollection).toFixed(2));
                    var currentDue = parseInt(invoiceAmount) - (parseInt($('#collectionAmount').val()) + previousCollection);
                    $('.currentDue').val(currentDue.toFixed(2));
                }

                // if (currentDue < 0)
                // {
                //     alert('Collection amount sholuld not be cross invoice amount!');
                //     $('.collectionAmount').val(parseInt(invoiceAmount - previousCollection).toFixed(2));
                // }
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
