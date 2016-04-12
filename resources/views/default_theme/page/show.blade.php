@extends('layout.2columns-with-ads')

@section('content')

	<h1><?=$page->title?></h1>	

	<div class="page-content">
		<?=$page->content?>
	</div>	
@endsection