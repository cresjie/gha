@extends('layout.2columns-with-ads')

@section('content')
	@if( isset($mail) )
		<div style="max-width:500px;margin: 50px auto">
			<h1 class="text-success">Check your email!</h1>
			<p>We've send an email.</p>
			<p>Please follow the steps in order to reset your password.</p>	
		</div>
	@else
		
		
		<div class="form-wrapper" style="max-width:300px;margin: 50px auto">
			<h1 class="gray">Enter your email</h1>
			<?=Form::open()?>
				<div class="form-group <?=$errors->has('email') ? 'has-error' : ''?>">
					<?=Form::email('email',null,['class' => 'form-control','placeholder' => 'Email'])?>
					<?=$errors->first('email','<p class="help-block">:message</p>')?>
				</div>

				<button class="btn btn-primary">Submit</button>
			<?=Form::close()?>
		</div>
		
	@endif
	
@endsection