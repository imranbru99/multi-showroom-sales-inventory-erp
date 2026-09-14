<form class="form-horizontal" action="{{ route($tab1Link) }}" method="POST" enctype="multipart/form-data"
      id="newProduct" name="newProduct">
    {{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">Add {{ $tab1 }}</h4>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect">{{ $buttonName }}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <input type="hidden" name="productId" value="{{ $productId }}">
            <input type="hidden" name="type" value="update">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('category') ? ' has-danger' : '' }}">
                        <label for="category">Category</label>
                        <select class="form-control chosen-select" id="category" name="category">
                            <option value="">Select Category</option>
                            @foreach($categories as $categoryInfo)
                            @php
                            if ($categoryInfo->id == $product->category_id)
                            {
                            $select = "selected";
                            }
                            else
                            {
                            $select = "";
                            }
                            @endphp
                            <option value="{{$categoryInfo->id}}" {{ $select }}>{{$categoryInfo->name}}</option>
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
                        <input type="text" class="form-control form-control-danger" name="productName"
                               value="{{ $product->name }}" required>
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
                        <input type="text" class="form-control form-control-danger" name="productCode"
                               value="{{ $product->code }}">
                        @if ($errors->has('productCode'))
                        @foreach($errors->get('productCode') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="product-model-no">Product Model No</label>
                    <div class="form-group {{ $errors->has('productModelNo') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="productModelNo"
                               value="{{ $product->model_no }}" required>
                        @if ($errors->has('productModelNo'))
                        @foreach($errors->get('productModelNo') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="productSize">Product Size</label>
                    <div class="form-group {{ $errors->has('productSize') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="productSize"
                               value="{{ $product->productSize }}">
                        @if ($errors->has('productModelNo'))
                        @foreach($errors->get('productSize') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="product-color">Product Color</label>
                    <div class="form-group {{ $errors->has('productColor') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="productColor"
                               value="{{ $product->color }}">
                        @if ($errors->has('productColor'))
                        @foreach($errors->get('productColor') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label for="lifting_price">Lifting Price</label>
                    <div class="form-group {{ $errors->has('lifting_price') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" id="lifting_price" name="lifting_price"
                               value="{{ $product->lifting_price }}" required>
                        @if ($errors->has('lifting_price'))
                        @foreach($errors->get('lifting_price') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="price">Price</label>
                    <div class="form-group {{ $errors->has('price') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" id="price" oninput="calculatePrice()" name="price"
                               value="{{ $product->price }}" required>
                        @if ($errors->has('price'))
                        @foreach($errors->get('price') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="mrp-sale">MRP Price</label>
                    <div class="form-group {{ $errors->has('mrpPrice') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" id="mrpPrice" name="mrpPrice"
                               value="{{ $product->mrp_price }}" required="">
                        @if ($errors->has('mrpPrice'))
                        @foreach($errors->get('mrpPrice') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="mrp-sale">Haire Price</label>
                    <div class="form-group {{ $errors->has('hairePrice') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" id="hairePrice" name="hairePrice"
                               value="{{ $product->haire_price }}">
                        @if ($errors->has('hairePrice'))
                        @foreach($errors->get('hairePrice') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="product-discount">Product Discount</label>
                    <div class="form-group {{ $errors->has('discount') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" name="discount"
                               value="{{ $product->discount }}">
                        @if ($errors->has('discount'))
                        @foreach($errors->get('discount') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="warranty">Warranty</label>
                    <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" name="warranty"
                               value="{{ $product->warranty }}">
                        @if ($errors->has('warranty'))
                        @foreach($errors->get('warranty') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="reorder-level-quantity">Reorder Level Quantity</label>
                    <div class="form-group {{ $errors->has('reorderQty') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" name="reorderQty"
                               value="{{ $product->reorder_level_qty }}">
                        @if ($errors->has('reorderQty'))
                        @foreach($errors->get('reorderQty') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    @php
                    $uoms = array('Pcs'=>'Pcs','Kg'=>'Kg','Liter'=>'Liter','Box'=>'Box','gm'=>'gm','cm'=>'cm');
                    @endphp
                    <label for="product-uom">UOM</label>
                    <select class="form-control" id="productUom" name="productUom">
                        <option value="">Select UOM</option>
                        @foreach($uoms as $key => $value)
                        @php
                        $select = "";
                        if ($key == $product->uom)
                        {
                        $select = "selected";
                        }
                        else
                        {
                        $select = "";
                        }
                        @endphp
                        <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('productUom'))
                    @foreach($errors->get('productUom') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
                <div class="col-md-4">
                    <label for="order-by">Order By</label>
                    <div class="form-group {{ $errors->has('orderBy') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" name="orderBy"
                               value="{{ $product->order_by }}">
                        @if ($errors->has('orderBy'))
                        @foreach($errors->get('orderBy') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="transport-point">Transport Point</label>
                    <div class="form-group {{ $errors->has('transportPoint') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control form-control-danger" name="transportPoint"
                               value="{{ $product->transport_point }}">
                        @if ($errors->has('transportPoint'))
                        @foreach($errors->get('transportPoint') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="publication-status">Publication status</label>
                    <div class="form-group {{ $errors->has('status') ? ' has-danger' : '' }}"
                         style="height: 40px; line-height: 40px;">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" value="1" name="status" id="published"
                                       {{ $product->status == 1 ? 'checked' : '' }} required> Published
                            </label>
                        </div>

                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" value="0" name="status" id="unpublished"
                                       {{ $product->status == 0 ? 'checked' : '' }}> Unpublished
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="publication-status">Product Type</label>
                    <div class="form-group {{ $errors->has('pdtType_status') ? ' has-danger' : '' }}"
                         style="height: 40px; line-height: 40px;">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" value="1" name="pdtType_status" id="spare_parts"
                                       {{ $product->pdtType_status == 1 ? 'checked' : '' }} required> Spare Parts
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" value="0" name="pdtType_status" id="finished_goods"
                                       {{ $product->pdtType_status == 0? 'checked' : '' }}> Finish Goods
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="publication-status">Product type</label>
                    <div class="form-group {{ $errors->has('pdtType_status') ? ' has-danger' : '' }}"
                         style="height: 40px; line-height: 40px;">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" name="old_product" @if ($product->old)
                                checked
                                @endif> Old Product
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-4">
                    <label for="tag-line">Tag Line</label>
                    <div class="form-group {{ $errors->has('tag') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="tag"
                               value="{{ $product->tag_line }}">
                        @if ($errors->has('tag'))
                        @foreach($errors->get('tag') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('power') ? ' has-danger' : '' }}">
                        <label for="power">Power</label>
                        <input type="text" class="form-control form-control-danger" name="power"
                               value="{{ $product->power}}">
                        @if ($errors->has('power'))
                        @foreach($errors->get('power') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('powerConsumption') ? ' has-danger' : '' }}">
                        <label for="powerConsumption">Power Consumption</label>
                        <input type="text" class="form-control form-control-danger" name="powerConsumption"
                               value="{{ $product->powerConsumption }}">
                        @if ($errors->has('powerConsumption'))
                        @foreach($errors->get('powerConsumption') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <!-- <div class="row">
        <div class="col-md-6">
            <label for="short-descriotion">Short description</label>
            <div class="form-group {{ $errors->has('shortDescription') ? ' has-danger' : '' }}">
                <textarea class="form-control form-control-danger" name="shortDescription" rows="5">{{ $product->short_description }}</textarea>
                @if ($errors->has('shortDescription'))
                    @foreach($errors->get('shortDescription') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <label for="long-description">Long description</label>
            <div class="form-group {{ $errors->has('longDescription') ? ' has-danger' : '' }}">
                <textarea class="form-control form-control-danger" name="longDescription" rows="5">{{ $product->long_description }}</textarea>
                @if ($errors->has('longDescription'))
                    @foreach($errors->get('longDescription') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-6">
                            <label for="youtube-link">Youtube Link</label>
                            <div class="form-group {{ $errors->has('youtubeLink') ? ' has-danger' : '' }}">
                                <input type="text" class="form-control form-control-danger" name="youtubeLink" value="{{  $product->youtube_link }}">
                                @if ($errors->has('youtubeLink'))
                                    @foreach($errors->get('youtubeLink') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                    </div>
    </div> -->

        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect">{{ $buttonName }}</button>
                </div>
            </div>
        </div>
    </div>
</form>