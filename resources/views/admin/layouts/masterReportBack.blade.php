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
	    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">

	    <title>Admin || {{ $title }}</title>

	    <link rel="icon" type="image/png" sizes="20x20" href="{{asset('public/uploads/admin_logo/small_logo.png')}}">

	    <style type="text/css">
	        .card-pad{
	            padding-bottom: 10px;
	        }

	        /*@media print
            {
             body * { visibility: hidden; }
            .printArea * { visibility: visible; }
             body * { padding-top: 0  !important; }

            }*/
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

	        <!-- Left Sidebar - style you can find in sidebar.scss  -->  
	        @include('admin.partials.menu')
	        <!-- End Left Sidebar - style you can find in sidebar.scss  -->

	        <!-- Page wrapper  -->
	        <div class="page-wrapper">
	            <!-- Container fluid  -->
	            <div class="container-fluid">
		            <!-- Bread crumb and right sidebar toggle -->
		            @yield('bread-crumb')
		            <!-- End Bread crumb and right sidebar toggle -->

				    <div style="padding-bottom: 30px;"></div>

    			    <form class="form-horizontal" id="searchForm" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
    			    	{{ csrf_field() }}

    				    <div class="card">
    				        {{-- <div class="card-header">
    				            <div class="row">
    				                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
    				                <div class="col-md-6 text-right">
    				                	<button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-search"></i> Search</button>
    				                </div>
    				            </div>
    				        </div>
 --}}
    				        <div class="card-body">
	    		                <!-- Search Card Body Content -->
	    		                @yield('search_card_body')
	    		                <!-- End Search Card Body Content -->    				        	
    				        </div>

    				        <div class="card-footer">
    				            <div class="row">
    				                <div class="col-md-12 text-right">
    				                	
    				                	<button type="submit" id="search" class="btn btn-outline-info btn-lg waves-effect search"><i class="fa fa-search"></i> Search</button>
    				                </div>
    				            </div>	        	
    				        </div>
    				    </div>
    				</form>

		            <div class="card" style="margin-bottom: 0px;">              
		                <div class="card-header">
		                    <div class="row">
		                        <div class="col-md-6"><h4 class="card-title">Back Parts</h4></div>
		                        <div class="col-md-6 text-right">

		                                @yield('print_card_header')

		                                <button  class="btn btn-outline-info btn-lg waves-effect" onclick="Clickheretoprint()" ><i class="fa fa-print"></i> Print</button>
		                         
		                        </div>
		                    </div>
		                </div>

		                <div class="card-body">
			                <!-- Print Card Body Content -->
			                @yield('print_card_body')
			                <!-- End Print Card Body Content -->		                	
		                </div>
		            </div>

	                <!-- Right sidebar -->
	                @include('admin.partials.right-sidebar')
	                <!-- End Right sidebar -->
	            </div>
	            <!-- End Container fluid  -->
	        </div>
	        <!-- End Page wrapper  -->

	        <!-- footer -->
	        <footer class="footer">
	            © {{ date('Y') }} Developed by <a target="_blank" href="http://www.technoparkbd.com/">Techno Park Bangladesh</a>           
	        </footer>
	        <!-- End footer -->

	    </div>
	    <!-- End Wrapper -->



	    @include('admin.partials.footer-assets')

	    <script>
	        $('#print').submit(function(){
	            if ($('#print_value').val() == "")
	            {
	                swal("Please! Search Data", "", "warning");
	                return false;
	            }
	        });
	    </script>

	    <script>
  function Clickheretoprint()
  {
	var schoolname = '{{Session::get("instituteName")}}';
	var comaddress = '{{Session::get("address")}}';
	var phone = '{{Session::get("phone")}}';
	var email = '{{Session::get("email")}}';
	var disp_setting = "toolbar=yes,location=no,directories=yes,menubar=yes,";
	disp_setting += "scrollbars=yes,width=1140, height=780, left=100, top=25";
	var docprint = window.open("about:blank", "_blank", disp_setting);
	var oTable;
	//console.log(selected_val);
	oTable = document.getElementById("printArea");
	docprint.document.open();
	docprint.document.write('<html><title>Transfer Certificate</title>');
	docprint.document.write('<head><style>');
	docprint.document.write('</style></head>');
	docprint.document.write('<body><center>');
	//docprint.document.write('<p style="color: red;text-align:center;font-size: 14px"><b>' + schoolname + '</b></p>');
	//docprint.document.write('<p style="margin-top:-10px;text-align:left;font-size: 12px">' + comaddress + '</p>');
	//docprint.document.write('<p style="margin-top:-10px;text-align:left;font-size: 12px">Email: ' + email + '</p>');
	//docprint.document.write('<p style="margin-top:-10px;text-align:left;font-size: 12px">Mobile: ' + phone + '</p>');
	docprint.document.write(oTable.innerHTML);
	docprint.document.write('</center></body></html>');
	docprint.document.close();
	docprint.print();
	docprint.close();
  }
</script>

	    <!-- This page plugins -->
	    @yield('custom-js')

	</body>
</html>