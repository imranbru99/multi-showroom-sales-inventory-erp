<form class="form-horizontal" action="{{ route($tab5Link) }}" method="POST" enctype="multipart/form-data" id="newProduct" name="newProduct">
	{{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">Add {{ $tab5 }}</h4></div>
                <div class="col-md-6 text-right">
                	<a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                		<i class="fa fa-arrow-circle-left"></i> Go Back
                	</a>
                	<button type="submit" class="btn btn-outline-info btn-lg waves-effect">{{ $buttonName }}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- <input type="hidden" name="productId" value="{{ @$productId }}"> --}}

            <div class="row">
                <div class="col-md-6">
                    <label for="tag-line">Tag Line</label>
                    <div class="form-group {{ $errors->has('tag') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control form-control-danger" placeholder="Tag line" name="tag" value="{{ old('tag') }}">
                        @if ($errors->has('tag'))
                            @foreach($errors->get('tag') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="slider-image">Slider image</label> 
                    <span style="color: red;">( Standard Image Size: 700px * 700px )</span>
                    <div class="form-group {{ $errors->has('sliderImage') ? ' has-danger' : '' }}">
                        <input type="file" class="form-control" id="sliderImage" aria-describedby="fileHelp" name="sliderImage">
                        @if ($errors->has('sliderImage'))
                            @foreach($errors->get('sliderImage') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                        <p class="uploadImage">
                        </p>
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