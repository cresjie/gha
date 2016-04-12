@extends('layout.2columns-with-ads')

@section('content')
	<div class="page-header">
		<h1>Sign up</h1>
	</div>

	<div style="max-width:300px">
		<?=Form::open(['route' => 'signup.store'])?>
			@if( $errors->any())
			<div class="alert alert-danger"> <?=$errors->count()?> Invalid Field(s)</div>
			@endif


			<div class="form-group <?=$errors->has('email') ? 'has-error' : ''?>">
				<?=Form::label('email','Email',['class' => 'control-label'])?><span class="required">*</span> :
				<?=Form::email('email',Input::old('email'),['class' => 'form-control','required'])?>
				<?=$errors->first('email','<p class="help-block">:message</p>')?>
			</div>

			<div class="form-group <?=$errors->has('password') ? 'has-error' : ''?>">
				<?=Form::label('password','Password',['class' => 'control-label'])?><span class="required">*</span> :
				<?=Form::password('password',['class' => 'form-control','required'])?>
				<?=$errors->first('password','<p class="help-block">:message</p>')?>
			</div>

			<div class="form-group <?=$errors->has('retype_password') ? 'has-error' : ''?>">
				<?=Form::label('retype_password','Retype Password',['class' => 'control-label'])?><span class="required">*</span> :
				<?=Form::password('retype_password',['class' => 'form-control','required'])?>
				<?=$errors->first('retype_password','<p class="help-block">:message</p>')?>
			</div>

			<div class="form-group <?=$errors->has('first_name') ? 'has-error' : ''?>">
				<?=Form::label('first_name','First Name',['class' => 'control-label'])?><span class="required">*</span> :
				<?=Form::text('first_name',null,['class' => 'form-control','required'])?>
				<?=$errors->first('first_name','<p class="help-block">:message</p>')?>
			</div>

			<div class="form-group <?=$errors->has('last_name') ? 'has-error' : ''?>">
				<?=Form::label('last_name','Last Name',['class' => 'control-label'])?><span class="required">*</span> :
				<?=Form::text('last_name',null,['class' => 'form-control','required'])?>
				<?=$errors->first('last_name','<p class="help-block">:message</p>')?>
			</div>

			
			<div class="form-horizontal <?=$errors->has('gender') ? 'has-error' : ''?>">
				<div class="form-group">
					<label class="control-label col-xs-4 " style="text-align:left">Gender<span class="required">*</span> :</label>
					<div class="col-xs-8">	
						<?=Form::select('gender',[''=> '--Select--','male' => 'Male','female' => 'Female'],null,['class' => 'form-control'])?>
						<?=$errors->first('gender','<p class="help-block">:message</p>')?>
					</div>
				</div>
			</div>
			

			

			<div class="form-group">
				<?=Form::submit('Register',['class' => 'btn btn-primary btn-lg'])?>

			</div>

		<?=Form::close()?>
	</div>
	


@endsection