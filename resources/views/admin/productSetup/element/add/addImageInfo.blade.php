<form class="form-horizontal" action="javascript:void(0)" method="POST" id="image-form" name="image-form" enctype="multipart/form-data">
	{{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">Add {{ $tab3 }}</h4></div>
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

            <div class="row">
                <div class="col-md-12">
                    <label for="product-image">Product Image</label> 
                    <span style="color: red;">( Standard Image Size: 700px * 700px )</span>
                    <div class="form-group {{ $errors->has('productImage') ? ' has-danger' : '' }}">
                        <input type="file" class="form-control" id="productImage" aria-describedby="fileHelp" name="productImage" required>
                        @if ($errors->has('productImage'))
                            @foreach($errors->get('productImage') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                        <p class="uploadImage">
                        </p>
                    </div>
                </div>
            </div>

            @php
            	use App\ProductImage;
            	$images = ProductImage::where('product_id',$productId)->get();
            @endphp

            <div id="images">
	            @foreach ($images as $image)
		            <div class="card card_image_{{ $image->id }}" style="width: 200px; display: inline-block;" align="center">
		            	<img class="card-img-top" src="{{ url($image->image) }}" alt="Card image" style="width:150px; height: 150px;">
		            	<div class="card-body">
		            		<a href="javascript:void(0)" data-id="{{ $image->id }}" data-token="{{ csrf_token() }}" class="btn btn-outline-danger" onclick="removeImage({{ $image->id }})" style="width: 100%;">Delete</a>
		            	</div>
		            </div>
	            @endforeach
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