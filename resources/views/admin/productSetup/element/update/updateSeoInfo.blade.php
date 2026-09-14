<form class="form-horizontal" action="{{ route($tab4Link) }}" method="POST" enctype="multipart/form-data" id="newProduct" name="newProduct">
	{{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">Add {{ $tab4 }}</h4></div>
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
                    <label for="meta-title">Meta Title</label>
                    <div class="form-group {{ $errors->has('metaTitle') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="metaTitle" value="{{ $product->meta_title }}" required>
                        @if ($errors->has('metaTitle'))
                            @foreach($errors->get('metaTitle') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="meta-keyword">Meta Keyword</label>
                    <div class="form-group {{ $errors->has('metaKeyword') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" name="metaKeyword" value="{{ $product->meta_keyword }}" required>
                        @if ($errors->has('metaKeyword'))
                            @foreach($errors->get('metaKeyword') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label for="meta-description">Meta Description</label>
                    <div class="form-group {{ $errors->has('metaDescription') ? ' has-danger' : '' }}">
                        <textarea class="form-control" name="metaDescription" rows="5" required>{{ $product->meta_description }}</textarea>
                        @if ($errors->has('metaDescription'))
                            @foreach($errors->get('metaDescription') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
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