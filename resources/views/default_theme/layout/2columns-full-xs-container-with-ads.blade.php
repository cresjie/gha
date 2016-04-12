<html @yield('html-attrs')>

	@include('html.head')
	
	<body class="@yield('body-class')" @yield('body-attrs')>

		@include('html.nav.header-nav')

		<div class="container full-xs-container">
			<div class="row">
				
				<div class="col-md-8 col-lg-9">
					@include('html.content-spinner')
					<div class="content-container">
						@yield('content')
					</div><!--/.content-container  -->
				</div>

				<div class="hidden-xs col-md-4 col-lg-3">
					<div class="h-spacer-10"></div>
					<div class="gha-ads-wrapper">
						Advertisement
					</div>
				</div>

			</div>
		</div>

		@include('html.footer')
	</body>

</html>