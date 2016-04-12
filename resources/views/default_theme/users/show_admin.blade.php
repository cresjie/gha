@extends('layout.2columns-full-xs-container-with-ads')

@section('stylesheets')
	@parent
	<?=HTML::style('css/library/cropper/cropper.min.css')?>
	<?=HTML::style('css/customcss/user/show.css')?>
@endsection

@section('scripts')
	@parent
	<?=HTML::script('js/library/angular/angular-route.min.js')?>
	<?=HTML::script('js/library/FileWizard/FileWizard-bundle.js')?>
	<?=HTML::script('js/library/cropper/cropper.min.js')?>
	<?=HTML::script('js/customjs/user/show.admin.js')?>
@endsection

@section('content')

	<div class="banner-wrapper">
		<img class="img-responsive" src="<?=Config::get('gha.upload.image.group_cover_img.base_url')?>/w850/group-cover-default.jpg">
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="profile-img-container">
				<div class="row">
					<div class="col-sm-3 col-lg-3">
						<div class="profile-img-wrapper">
							<div class="profile-img">
								<a class="change-profile-img" href="">Change</a>
								<img class="img-responsive" src="<?=App\Helpers\Upload\Image::getImage($user->profile_img,'user_profile_img',200)?>">
							</div>
							
						</div>
					</div>
					<div class="col-sm-6 col-lg-6">
						<div class="user-info-wrapper">
							<h1 class="user-info-name"><?=$user->first_name?> <?=$user->last_name?></h1>
							<p>I love to code</p>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
	</div> <!-- /.row-->

	<div class="tabs-flat-container" ng-controller="TabController">
		<ul class="nav nav-tabs nav-tabs-flat text-center">
			<li ng-class="{active:isCurrentTab('/')}"><a href="#/">Current<br>Events</a></li>
			<li ng-class="{active:isCurrentTab('/progressive-events')}"><a href="#/progressive-events">Progressive<br>Events</a></li>
			<li ng-class="{active:isCurrentTab('/previous-events')}"><a href="#/previous-events">Previous<br>Events</a></li>
			<li ng-class="{active:isCurrentTab('/calendar-of-events')}"><a href="#/calendar-of-events">Calendar<br>of Events</a></li>
			<li ng-class="{active:isCurrentTab('/contacts')}"><a href="#/contacts">My<br>Contacts</a></li>
			
		</ul>
	</div>

	<div class="tab-content">
		<div class="content-spinner tab-content-spinner"  style="display:none">
			<div class="spinner-wrapper">
				<i class="loading fa fa-refresh animate-spin"></i>
			</div>
		</div>

		<div ng-view></div>
		
	</div>

	<div id="change-profile-dialog" class="modal fade" style="display:none">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Profile picture</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="profile-cropper-container">
								<img class="profile-cropper img-responsive" src="">
							</div>
						</div>
						<div class="col-sm-6">
							<label>Preview:</label>
							<div class="row">
								<div class="col-sm-6">
									<div class="pad-10">
										<div class="cropper-preview circle"></div>
									</div>
									
								</div>
								<div class="col-sm-6">
									<div class="pad-10">
										<div class="cropper-preview round-corner"></div>
									</div>
									
								</div>
							</div>
							
							
							
						</div>
					</div>

					<div class="progress progress-striped" style="margin-top:10px;display:none">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"> <span class="sr-only"></span> </div> 
					</div>
					<div class="content-spinner profile-content-spinner">

						<div class="spinner-wrapper">
							<i class="loading fa fa-refresh animate-spin"></i>
						</div>
					</div>
				</div>
				<div class="modal-footer">

					<button id="upload-profile" class="btn btn-primary">Upload</button>
					<button class="btn btn-gray" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
@endsection