@extends('admin.layouts.masterAddEdit')

@section('card_body')
<div class="card-body">
    <div class="row">
        <div class="col-md-4"> 
            <div class="form-group {{ $errors->has('productType') ? ' has-danger' : '' }}">
                <label for="productType">Product Type</label>
                <select class="form-control chosen-select" id="productType" name="productType" required>
                    @foreach($productTypes as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('productType'))
                @foreach($errors->get('productType') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4"> 
            <div class="form-group {{ $errors->has('category') ? ' has-danger' : '' }}">
                <label for="category">Category</label>
                <select class="form-control chosen-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $categoryInfo)
                    <option value="{{ $categoryInfo->id }}">{{ $categoryInfo->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('category'))
                @foreach($errors->get('category') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">                 
            <div class="form-group {{ $errors->has('productName') ? ' has-danger' : '' }}">
                <label for="product-name">Product Name</label>
                <input type="text" class="form-control form-control-danger" name="productName" value="{{ old('productName') }}" required>
                @if ($errors->has('productName'))
                @foreach($errors->get('productNname') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="product-code">Product Code</label>
            <div class="form-group {{ $errors->has('productCode') ? ' has-danger' : '' }}">
                <input type="text" class="form-control form-control-danger" name="productCode" value="{{ old('productCode') }}">
                @if ($errors->has('productCode'))
                @foreach($errors->get('productCode') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="product-model-no">Product Model No</label>
            <div class="form-group {{ $errors->has('productModelNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control form-control-danger" name="productModelNo" value="{{ old('productModelNo') }}" required>
                @if ($errors->has('productModelNo'))
                @foreach($errors->get('productModelNo') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="productSize">Product Size / Color</label>
            <div class="form-group {{ $errors->has('productSize') ? ' has-danger' : '' }}">
                <input type="text" class="form-control form-control-danger" name="productSize" value="{{ old('productSize') }}">
                @if ($errors->has('productSize'))
                @foreach($errors->get('productSize') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="price">Default Lifting Price</label>
            <div class="form-group {{ $errors->has('price') ? ' has-danger' : '' }}">
                <input type="number" class="form-control form-control-danger" id="price" name="price" value="0">
                @if ($errors->has('price'))
                @foreach($errors->get('price') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="mrp-sale">Retail Price</label>
            <div class="form-group {{ $errors->has('mrpPrice') ? ' has-danger' : '' }}">
                <input type="number" class="form-control form-control-danger" id="mrpPrice" name="mrpPrice" required>
                @if ($errors->has('mrpPrice'))
                @foreach($errors->get('mrpPrice') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="mrp-sale">Installment Price</label>
            <div class="form-group {{ $errors->has('hairePrice') ? ' has-danger' : '' }}">
                <input type="number" class="form-control form-control-danger" id="hairePrice" name="hairePrice">
                @if ($errors->has('hairePrice'))
                @foreach($errors->get('hairePrice') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-2">
            <label for="reorder-level-quantity">Reorder Level</label>
            <div class="form-group {{ $errors->has('reorderQty') ? ' has-danger' : '' }}">
                <input type="number" class="form-control form-control-danger" name="reorderQty" value="{{ old('reorderQty') }}">
                @if ($errors->has('reorderQty'))
                @foreach($errors->get('reorderQty') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-2">
            @php
            $uoms = array('Pcs'=>'Pcs','Kg'=>'Kg','Liter'=>'Liter','Box'=>'Box','gm'=>'gm','cm'=>'cm');
            @endphp
            <label for="product-uom">UOM</label>
            <select class="form-control" id="productUom" name="productUom">
                <option value="">Select UOM</option>
                @foreach($uoms as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @if ($errors->has('productUom'))
            @foreach($errors->get('productUom') as $error)
            <div class="form-control-feedback">{{ $error }}</div>
            @endforeach
            @endif
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-6">
            <label class="warranty" for="warranty">Warranty (Month)</label>
            <div class="form-group warranty {{ $errors->has('warranty') ? ' has-danger' : '' }}" id="warranty">
                <input type="number" class="form-control form-control-danger" name="warranty" value="{{ old('warranty') }}">
                @if ($errors->has('warranty'))
                @foreach($errors->get('warranty') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-6">
            <label for="transport-point">Transport Point</label>
            <div class="form-group {{ $errors->has('transportPoint') ? ' has-danger' : '' }}">
                <input type="number" class="form-control form-control-danger" name="transportPoint" value="{{ old('transportPoint') }}">
                @if ($errors->has('transportPoint'))
                @foreach($errors->get('transportPoint') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

            </div>
        </div>
        
        <div class="col-md-4">
            <label for="publication-status">Publication Status</label>
            <div class="form-group {{ $errors->has('status') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="1" name="status" id="published" required checked> Published
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="0" name="status" id="unpublished" > Unpublished
                    </label>
                </div>
            </div>
        </div>
        
        

    </div>
</div>
@endsection

@section('custom-js')

<script type="text/javascript">
    $('#productType').change(function () {
        var type = $(this).val();
            if (type == 'warranty_product') {
                $('.warranty').show();
            } else {
                $('.warranty').hide();
            }
    });
</script>
@endsection
