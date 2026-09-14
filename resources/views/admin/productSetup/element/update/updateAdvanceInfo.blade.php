<form class="form-horizontal" action="{{ route($tab2Link) }}" method="POST" enctype="multipart/form-data" id="newProduct" name="newProduct">
	{{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">Add {{ $tab2 }}</h4></div>
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
            	<div class="col-md-6">
            		@php
            			$productSections = array('New Arrival'=>'New Arrival','Featured Product'=>'Featured Product','Top Rated'=>'Top Rated','Best Seller'=>'Best Seller');
                        $relatedProductId = explode(',', @$productAdvance->related_product_id);
                        $sections = explode(',', @$productAdvance->product_section);
            		@endphp
            		<label for="product-section">Product Section</label>
            		<select class="form-control chosen-select" name="sections[]" data-placeholder="Select Product Section" multiple>
            			@foreach ($productSections as $key => $value)
                            @php
                                $select = "";
                                if (in_array($key,$sections))
                                {
                                    $select = 'selected';
                                }
                                else
                                {
                                    $select = "";
                                }
                                
                            @endphp
            				<option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
            			@endforeach
            		</select>
            	</div>

                <div class="col-md-6">
                    <label for="related-product">Related Product</label>
                    <div class="form-group">
                        <select name="relatedProduct[]" data-placeholder="Select Related Products" class="form-control chosen-select" multiple>
                            @foreach($relatedProducts as $products)
                                @php
                                    $select = "";
                                    if (in_array($products->id, $relatedProductId))
                                    {
                                        $select = "selected";
                                    }
                                    else
                                    {
                                        $select = "";
                                    }
                                @endphp
                                <option value="{{ $products->id }}" {{ $select }}>{{ $products->name }} ({{ $products->code }})</option>
                            @endforeach
                        </select>

                        @if ($errors->has('relatedProduct'))
                            @foreach($errors->get('relatedProduct') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                	<label for="pre-order">Preorder</label>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-danger" placeholder="Preorder Duration" name="preOrderDuration" value="{{ @$productAdvance->pre_order_duration }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="free-shippimg">Shipping</label>
                    <div class="form-group" style="height: 40px; line-height: 40px;">
                        <div class="form-check-inline">
                            <label class="form-check-label" for="shiooing">
                                <input class="form-check-input" type="checkbox" name="shipping" value="free" {{ @$productAdvance->shipping == "free" ? 'checked' : '' }}>Free Shipping
                            </label>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Hot Deal Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check-inline">
                        <label class="form-check-label" for="hot-deal">
                            <input class="form-check-input" type="checkbox" name="hotDeal" value="" id="hotInput" {{ @$productAdvance->hot_discount != "" ? 'checked' : '' }}>
                            <label for="hot-deal">Hot Deal</label>
                        </label>
                    </div>
                    <div class="form-group">
                        <span style="display: none;" id="hotDeal" class="hotDeal">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-danger" placeholder="Discount For Hot Deal" name="hotDiscount" value="{{ @$productAdvance->hot_discount }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-danger datepicker" placeholder="Date" name="hotDate" value="{{ @$productAdvance->hot_discount_date }}">
                                </div>
                            </div>                                                
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check-inline">
                        <label class="form-check-label" for="hot-deal">
                            <input class="form-check-input" type="checkbox" id="specialInput" name="specialDeal" value="" {{ @$productAdvance->special_discount != "" ? 'checked' : '' }}>
                            <label for="special-deal">Special Deal</label>
                        </label>
                    </div>
                    <div class="form-group">
                        <span style="display: none;" id="specialDeal" class="specialDeal">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-danger" placeholder="Discount For Special Deal" name="specialDiscount" value="{{ @$productAdvance->special_discount }}">
                                </div>

                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-danger datepicker" placeholder="Date" name="specialDate" value="{{ @$productAdvance->special_discount_date }}">
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
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