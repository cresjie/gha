@extends('layout.2columns-with-ads')

@section('content')
	@if( $valid )
		<div style="max-width:300px;margin:50px auto">
			
			@if( isset($reset) )
				<?=Form::model($reset)?>
			@else
				<?=Form::open()?>	
			@endif
				<?=Form::hidden('user_id')?>
				<?=Form::hidden('token')?>
				<div class="form-group <?=$errors->has('new_password') ? 'has-error' : ''?>">
					<label class="control-label">New password</label>
					<?=Form::password('new_password',['class' => 'form-control'])?>
					<?=$errors->first('new_password','<p class="help-block">:message</p>')?>
				</div>

				<div class="form-group <?=$errors->has('retype_new_password') ? 'has-error' : ''?>">
					<label class="control-label">Retype new password</label>
					<?=Form::password('retype_new_password',['class' => 'form-control'])?>
					<?=$errors->first('retype_new_password','<p class="help-block">:message</p>')?>
				</div>

				<div class="btn-action-group">
					<button class="btn btn-primary">Reset</button>
				</div>
			<?=Form::close()?>
		</div>
	@else
		<div class="text-center offset-top-50">
			<i class="fa fa-times-circle-o text-danger emblem-lg"></i>
			<h1>Invalid token</h1>
		</div>
		
	@endif
@endsection