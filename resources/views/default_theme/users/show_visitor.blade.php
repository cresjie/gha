@extends('layout.2columns-full-xs-container-with-ads')

@section('stylesheets')
	@parent
	<?=HTML::style('css/customcss/user/show.css')?>
@endsection

@section('scripts')
	@parent
	<script>
		<?php if( $contact ): ?>
			Storage.store('contact', <?=$contact?>);
		<?php endif; ?> 
	</script>
	<?=HTML::script('js/customjs/user/show.visitor.js')?>

@endsection
@section('content')

	<div class="banner-wrapper">
		<img class="img-responsive" src="http://localhost/gci/w850/group-cover-default.jpg">
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="profile-img-container">
				<div class="row">
					<div class="col-sm-3 col-md-3">
						<div class="profile-img-wrapper">
							<div class="profile-img">
								<img class="img-responsive" src="<?=App\Helpers\Upload\Image::getImage($user->profile_img,'user_profile_img',200)?>">
							</div>
							
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						<div class="user-info-wrapper">
							<h1><?=$user->first_name?> <?=$user->last_name?></h1>
							<p>asdf asdfasdf asdfasd fasdfas asdfasd f</p>
						</div>
					</div>
					<div class="col-sm-3 col-md-3" ng-controller="UserInteractController">
						<ul class="list-inline user-interact-wrapper text-right">
							<li>
								<a href><i class="interact-icon fa fa-envelope"></i></a>
							</li>
							<li>
								<a ng-if="!contact" ng-click="addContact('<?=$user->id?>')" href><i class="interact-icon fa fa-user"></i></a>
								<div ng-if="contact">
										<a ng-if="contact.requestor == me.id && !contact.is_confirmed" ng-click="deleteContact()" href="">cancel request</a>
										<div ng-if="contact.requestor != me.id && !contact.is_confirmed">
											<a  ng-click="confirmContact()" href="">confirm</a>
											<a ng-click="deleteContact()" href="">delete</a>
										</div>
										<div ng-if="contact.is_confirmed">
											<a ng-click="deleteContact()" href="">remove</a>
										</div>
										
										

								</div>
								
							</li>
						</ul>
						

					</div>
				</div>
			</div>
		</div>
		
	</div> <!-- /.row-->

	<div class="tabs-flat-container">
		<ul class="nav nav-tabs nav-tabs-flat text-center">
			<li><a data-toggle="tab" href>Current<br>Events</a></li>
			<li><a data-toggle="tab" href>Previous<br>Events</a></li>
		</ul>
	</div>
	
@endsection