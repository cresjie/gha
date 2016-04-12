@extends('layout.2columns-with-ads')

@section('content')

	<div class="panel panel-<?=$success ? 'success' : 'danger'?>" style="margin-top: 100px">
		<div class="panel-body">
			@if($success)
					<p>Your account has been <strong>verified</strong>. Thanks for your cooperation</p>
					<p><a href="<?=URL::route('login.index')?>">Login</a></p>
			@else
				<div class="text-center">
					<i class="fa fa-times-circle-o text-danger emblem-lg"></i>
					<h1 ><?=$message?></h1>
				</div>
				
			@endif
		</div>
	</div>
@endsection