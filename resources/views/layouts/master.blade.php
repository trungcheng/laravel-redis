@include('partials.header')

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

	    @include('partials.main_header')

	    @include('partials.main_sidebar')
	    <div class="content-wrapper">
	    	@yield('main_content')
	    </div><!-- /.content-wrapper -->

	    <footer class="main-footer">
	        <div class="pull-right hidden-xs">
	          <b>Version</b> 2.1.0
	        </div>
	        <strong>Copyright &copy; 2015-2016 <a href="http://blogtamsu.vn">BlogTamSu.Vn</a>.</strong> All rights reserved.
	    </footer>

	</div><!-- ./wrapper -->
	
	@include('partials.footer_script')

	@yield('custom_footer')
  </body>
</html>