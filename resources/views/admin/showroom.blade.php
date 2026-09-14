<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/')}}public/uploads/admin_logo/logo_small.jpg">

        <title>Admin || {{ $title }}</title>

        <link rel="icon" type="image/png" sizes="20x20" href="{{asset('/')}}public/uploads/admin_logo/logo_small.jpg">

        <style type="text/css">
            .card-pad{
                padding-bottom: 10px;
            }
        </style>
        @yield('custom_css')

        @include('admin.partials.header-assets')
    </head>
    <body class="skin-default fixed-layout">
        <!-- Preloader - style you can find in spinners.css -->
        @include('admin.partials.preloader')
        <!-- Main wrapper - style you can find in pages.scss -->
        <div id="main-wrapper">
            <!-- Topbar header - style you can find in pages.scss -->
            <header class="topbar">
                @include('admin.partials.top-navbar')
            </header>
            <!-- End Topbar header -->

            <div class="container" style="padding-top: 100px;">
			    <form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data" name="form">
			    	{{ csrf_field() }}

				    <div class="card">
				        <div class="card-header">
				            <div class="row">
				                <div class="col-md-12"><h4 class="card-title">{{ $title }}</h4></div>
				            </div>
				        </div>

				        <div class="card-body">
				        	<div class="row">
				        		<div class="col-md-10">
					                <div class="form-group">
					                    <label for="Showroom">Showroom</label>
					                    <select class="form-control" name="showroom" required>
					                        <option value="">Select Showroom</option>
                                            @foreach($showrooms as $showroom)
                                            @if ($showroom->id !== 3)
                                            <option value="{{$showroom->id}}">{{$showroom->name}}</option>
                                            @endif
					                        @endforeach
					                    </select>
					                </div> 
				        		</div>

				        		<div class="col-md-2 text-right">
					                <div class="form-group">
					                    <label for=""></label>
				                		<button type="submit" class="btn btn-outline-info btn-md waves-effect" style="width: 100%;"><i class="fa fa-play"></i> Go</button>
					                </div> 
				        		</div>
				        	</div>
				        </div>
				    </div>
				</form>

            <!-- footer -->
            <footer class="footer">
                © {{ date('Y') }} Developed by <a target="_blank" href="http://www.technoparkbd.com/">Techno Park Bangladesh</a>           
            </footer>
            <!-- End footer -->
            	
            </div>

        </div>
        <!-- End Wrapper -->



        @include('admin.partials.footer-assets')

        <!-- This page plugins -->
        @yield('custom-js')

    </body>
</html>