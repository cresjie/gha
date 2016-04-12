@extends('layout.full-width')

@section('stylesheets')
	@parent
	<?=HTML::style('css/customcss/home/index.css')?>
	<style>
		.cat-party{
			background-image: url(<?=asset('images/event-category/cat-party.jpg')?>);
		}
		.cat-art{
			background-image: url(<?=asset('images/event-category/cat-art.jpg')?>);
		}
		.cat-concert{
			background-image: url(<?=asset('images/event-category/cat-concert.jpg')?>);
		}
		.cat-tech{
			background-image: url(<?=asset('images/event-category/cat-concert.jpg')?>);
		}
	</style>
@endsection


@section('content')
	<div class="jumbotron home-jumbotron" style="background-image:url(<?=asset('images/banner/event-party2.jpg')?>)">
		<div class="container">
			<h1>Create, Organize, Manage</h1>
			<p>Enhances teamwork with effective administrative tools for organizing all your events better.</p>
		</div>
	</div>

	<div class="container">
		<h2>Categories</h2>
		<div class="row">
			<div class="col-sm-8">
				<a class="category-link" href="">
					<div class="cat-wrapper cat-party transition-500">
					<h3><span class="category-name">Festivals|Parties</span></h3>	
					</div>
					
				</a>
			</div>
			<div class="col-sm-4">
				<a class="category-link" href="">
					<div class="cat-wrapper cat-art transition-500">
					<h3><span class="category-name">Arts|Exhibitions</span></h3>	
					</div>
					
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<a class="category-link" href="">
					<div class="cat-wrapper cat-concert transition-500">
					<h3><span class="category-name">Concerts</span></h3>	
					</div>
					
				</a>
			</div>
			<div class="col-sm-4">
				<a class="category-link" href="">
					<div class="cat-wrapper cat-concert transition-500">
					<h3><span class="category-name">Workshops</span></h3>	
					</div>
					
				</a>
			</div>
			<div class="col-sm-4">
				<a class="category-link" href="">
					<div class="cat-wrapper cat-tech transition-500">
					<h3><span class="category-name">Online</span></h3>	
					</div>
					
				</a>
			</div>
		</div>
	</div>
	

@endsection