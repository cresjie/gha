@extends('layout.full-width')

@section('stylesheets')
	@parent
	<?=HTML::style('css/customcss/error/error.css')?>
@endsection
@section('content')
	<div class="container">
		<div class="error-wrapper text-center">
			<h1 class="error-code">404</h1>
			<p class="error-msg">Page not found.</p>
		</div>
	</div>
@endsection