<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Gighubapp Login</title>
		<?=HTML::style('css/bootstrap-united.min.css')?>
		<?=HTML::style('css/library/gighubapp/gha-framework.css')?>
		<?=HTML::script('js/library/jquery/jquery-1.10.1.min.js')?>
		<?=HTML::script('js/library/jquery/jquery-ui.min.js')?>
		<script>
			$(function(){
				if( !$('#email').val() )
					$('#email').focus();
				else
					$('#password').focus();
			});
		</script>
		
		@if( $errors->has('error_msg') )
			<script>
				$(function(){
					$('.form-wrapper').effect('shake');	
				});
				
			</script>
		@endif
	</head>
	<body>
		
		<div class="container">
			<div class="signin-wrapper">
				<div class="pad-10 text-center">
					<a href="<?=URL::route('home')?>">
						<img class="img " width="250" src="<?=asset('images/logo/main-logo.png')?>" alt="gighubapp logo">
					</a>
				</div>

				@if( $errors->has('error_msg') )
				<blockquote class="error">
					<?=$errors->first('error_msg')?>
				</blockquote>
				@endif
				
				<div class="form-wrapper">

					<div class="signin-form-wrapper">
						<?=Form::open(['route' => 'login.store'])?>
							<?=Form::hidden('_redirect')?>
							<div class="form-group">
								<label>Email</label>
								<?=Form::email('email',null,['class' => 'form-control','id' => 'email','required'])?>
							</div>

							<div class="form-group">
								<label>Password</label>
								<?=Form::password('password',['class' => 'form-control','id' => 'password'])?>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="checkbox">
										<label>
											<?=Form::checkbox('remember_me',1,null)?> Remember me
										</label>
									</div>
								</div>
								<div class="col-md-6">
									<a style="margin:10px 0;display:block" href="<?=URL::to('password/forgot')?>">Forgot password</a>
								</div>			
							</div>

							<div class="form-group">
								<button class="btn btn-primary">Login</button>
							</div>
						<?=Form::close()?>
					</div>
				</div>

				<div class="form-group">
					<a href="javascript:history.back()">Back</a>
					<a href="<?=URL::route('signup.index')?>" class="pull-right">Sign up</a>
				</div>		
			</div>
		</div>

	</body>
</html>