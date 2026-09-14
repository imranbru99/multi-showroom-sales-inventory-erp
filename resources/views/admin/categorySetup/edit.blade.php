@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
    	<input type="hidden" name="categoryId" value="{{ $category->id }}">
    	<input type="hidden" name="previousCategoryCoverImage" value="{{ $category->cover_image }}">
    	<input type="hidden" name="previousCategoryImage" value="{{ $category->image }}">

    	<div class="row">
    		<div class="col-md-6">
		    	<div class="form-group {{ $errors->has('parent') ? ' has-danger' : '' }}">
		            <label for="parent">Parent</label>
	                <select class="form-control chosen-select" name="parent">
	                    <option value="">Select Parent</option>
	                    @foreach($categories as $categoryInfo)
	                    	@php
	                    		$select = "";
	                    		if ($categoryInfo->id == $category->parent)
	                    		{
	                    			$select = "selected";
	                    		}
	                    		else
	                    		{
	                    			$select = "";
	                    		}
	                    		
	                    	@endphp
	                        <option value="{{ $categoryInfo->id }}" {{ $select }}>{{ $categoryInfo->name }}</option>
	                    @endforeach
	                </select>
	                @if ($errors->has('parent'))
	                    @foreach($errors->get('parent') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
    		</div>

    		<div class="col-md-6">
		        <div class="form-group {{ $errors->has('categoryName') ? ' has-danger' : '' }}">
		            <label for="name">name</label>
	                <input type="text" class="form-control" placeholder="Category name" name="categoryName" value="{{ $category->name }}" required>
	                @if ($errors->has('categoryName'))
	                    @foreach($errors->get('categoryName') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
    		</div>
    	</div>

    	<div class="row">
    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-12">
					    <div class="form-group {{ $errors->has('categoryCoverImage') ? ' has-danger' : '' }}">
					        <label for="cover-image">Cover Image</label> <span style="color: red; font-weight: bold;">( Height : 200px, Witdht : 1180px )</span>
				            <input type="file" class="form-control" name="categoryCoverImage" value="{{ old('categoryCoverImage') }}">
				            @if ($errors->has('categoryCoverImage'))
				                @foreach($errors->get('categoryCoverImage') as $error)
				                    <div class="form-control-feedback">{{ $error }}</div>
				                @endforeach
				            @endif
					    </div>
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-md-12">
    					<div class="form-group">
    						<img src="{{ url($category->cover_image) }}" class="img-thumbnail" alt="Category Cover Image" width="100px" height="100px">
    					</div>
    				</div>
    			</div>
    		</div>

    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-12">
				        <div class="form-group {{ $errors->has('categoryImage') ? ' has-danger' : '' }}">
				            <label for="image">Image</label>
			                <input type="file" class="form-control" placeholder="Category Image" name="categoryImage" value="{{ old('categoryImage') }}">
			                @if ($errors->has('categoryImage'))
			                    @foreach($errors->get('categoryImage') as $error)
			                        <div class="form-control-feedback">{{ $error }}</div>
			                    @endforeach
			                @endif
				        </div>
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-md-12">
    					<div class="form-group">
    						<img src="{{ url($category->image) }}" class="img-thumbnail" alt="Category Image" width="100px" height="100px">
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>

    	<div class="row">
    		<div class="col-md-6">
		        <div class="form-group {{ $errors->has('orderBy') ? ' has-danger' : '' }}">
		            <label for="order-by">Order By</label>
	                <input type="number" class="form-control form-control-danger" name="orderBy" value="{{ $category->order_by }}">
	                @if ($errors->has('orderBy'))
	                    @foreach($errors->get('orderBy') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
    		</div>

    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-6">
    					<label for="show-in-homepage">Show In Homepage</label>
				        <div class="form-group {{ $errors->has('showInHomepage') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
				        	@php
				        		$yesCheck = "";
				        		$noCheck = "";
				        		if ($category->show_in_home_page == 'Yes')
				        		{
				        			$yesCheck = 'checked';
				        		}
				        		else
				        		{
				        			$noCheck = 'checked';
				        		}				        		
				        	@endphp

				        	<div class="form-check-inline">
				        		<label class="form-check-label" for="yes">
				        			<input type="radio" class="form-check-input" name="showInHomepage" value="Yes" id="yes" {{ $yesCheck }}>Yes
				        		</label>
				        	</div>
				        	<div class="form-check-inline">
				        		<label class="form-check-label" for="no">
				        			<input type="radio" class="form-check-input" name="showInHomepage" value="No" id="no" {{ $noCheck }}>No
				        		</label>
				        	</div>
				        </div>
    				</div>
    				<div class="col-md-6">
    					<label for="publication-status">Publication Status</label>
				        <div class="form-group {{ $errors->has('categoryStatus') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
				        	@php
				        		$publishedCheck = "";
				        		$unpublishedCheck = "";
				        		if ($category->show_in_home_page == 'Yes')
				        		{
				        			$publishedCheck = 'checked';
				        		}
				        		else
				        		{
				        			$unpublishedCheck = 'checked';
				        		}				        		
				        	@endphp
				        	<div class="form-check-inline">
				        		<label class="form-check-label" for="published">
				        			<input type="radio" class="form-check-input" name="categoryStatus" value="1" id="published" {{ $publishedCheck }}>Published
				        		</label>
				        	</div>
				        	<div class="form-check-inline">
				        		<label class="form-check-label" for="unpublished">
				        			<input type="radio" class="form-check-input" name="categoryStatus" value="0" id="unpublished" {{ $unpublishedCheck }}>Unpublished
				        		</label>
				        	</div>
				        </div>
    				</div>
    			</div>
    		</div>
    	</div>

    	<div class="row">
    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-12">
				        <div class="form-group {{ $errors->has('metaTitle') ? ' has-danger' : '' }}">
				            <label for="meta-title">Meta Title</label>
			                <input type="text" class="form-control form-control-danger" placeholder="Meta Title" name="metaTitle" value="{{ $category->meta_title }}">
			                @if ($errors->has('metaTitle'))
			                    @foreach($errors->get('metaTitle') as $error)
			                        <div class="form-control-feedback">{{ $error }}</div>
			                    @endforeach
			                @endif
				        </div>
    				</div>
    			</div>

    			<div class="row">
    				<div class="col-md-12">
				        <div class="form-group {{ $errors->has('metaKeyword') ? ' has-danger' : '' }}">
				            <label for="meta-keyword">Meta keyword</label>
			                <input type="text" class="form-control form-control-danger" placeholder="Meta Keyword" name="metaKeyword" value="{{ $category->meta_keyword }}">
			                @if ($errors->has('metaKeyword'))
			                    @foreach($errors->get('metaKeyword') as $error)
			                        <div class="form-control-feedback">{{ $error }}</div>
			                    @endforeach
			                @endif
				        </div>
    				</div>
    			</div>
    		</div>
    		<div class="col-md-6">
		        <div class="form-group {{ $errors->has('metaDescription') ? ' has-danger' : '' }}">
		            <label for="meta-description">Meta description</label>
	                <textarea class="form-control" rows="5" name="metaDescription">{{ $category->meta_description }}</textarea>
	                @if ($errors->has('metaDescription'))
	                    @foreach($errors->get('metaDescription') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
    		</div>
    	</div>
    </div>
@endsection