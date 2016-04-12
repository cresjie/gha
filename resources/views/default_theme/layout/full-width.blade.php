<html @yield('html-attrs')>

	@include('html.head')
	
	<body class="@yield('body-class')" @yield('body-attrs')>

		@include('html.nav.header-nav')
		@include('html.content-spinner')
		<div class="content-container">
			@yield('content')
		</div> <!--/.content-container  -->

		@include('html.footer')
	</body>

</html>