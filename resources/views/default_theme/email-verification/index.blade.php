@extends('layout.2columns-with-ads')

@section('content')
	
	@if( Cookie::get('user_id') )
		<div class="panel panel-success" style="margin-top:100px">

			<div class="panel-body">
				<h1><strong>Thank you!</strong> <small>for registering to GigHubApp</small> </h1>

				<p>We've sent a message to your email. You need to <strong style="font-size:20px;color:#14A669">Verify your Email</strong> for authentic account and security reasons</p>

				<br></br>

				@if($mailResult)
					<p>Didn't receive an email? <a href="{{Request::url()}}">Resend verification</a></p>
				@else
					<p>Something went wrong while sending, <a href="{{Request::url()}}">Resend verification</a></p>
				@endif

			</div>
		</div>
	@else
		<div class="panel panel-danger" style="margin-top:100px">
			<div class="panel-body">
				<h4>Cookie has expired</h4>

				<p>We cant send verification link to your email</p>
				<p>Please send an email regarding with your account to <a href="mailto:accounts@gighubapp.com">accounts@gighubapp.com</a></p>
			</div>
		</div>
	@endif
	
@endsection