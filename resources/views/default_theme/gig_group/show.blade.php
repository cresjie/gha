@extends('layout.2columns-full-xs-container-with-ads')

@section('stylesheets')
	@parent
	<?=HTML::style('css/gig_group/show.css')?>
@endsection

@section('scripts')
	@parent
	<?=HTML::script('js/library/angular/angular-route.min.js')?>
	<?=HTML::script('js/customjs/gig_group/show.js')?>
	<script class="data-script">
		Storage.store('gig_group', <?=$gig_group->toJson()?>);
		Storage.store('the_member', <?=$the_member ? $the_member->toJson() : '' ?>);
	</script>
@endsection

@section('content')
	<div class="group-cover-wrapper" style="background:url(<?=App\Helpers\Upload\Image::getImage($gig_group->cover_img,'group_cover_img',850)?>)">
		<div class="group-cover-bottom">
			<div class="row table-row">
				<div class="col-sm-7 col">
					<div class="group-name-wrapper">
						<h1 class="group-name"><?=$gig_group->name?></h1>
						<small><?=$gig_group->slogan?></small>
					</div>
				</div>
				<div class="col-sm-5 col">
					<div class="pull-right" style="margin-top:5px;">
						<ul class="nav nav-pills">
							@if( $the_member )
								<li class="active dropdown">
									
									<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">Members</a>
									
									
								</li>
								<li class="active">
									<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false" title="edit group setting"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="">Create event</a></li>
										<li><a href="">Group Settings</a></li>
										<li class="leave-group-wrapper"><a class="leave-group" href="#" data-uid="">Leave group</a></li>
									</ul>
								</li>
							@endif
						</ul>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div>
		<ul id="tabs" class="nav nav-tabs gig-group-navs">
			<li><a data-toggle="tab" href>Current <br>Events</a></li>
			<li><a data-toggle="tab" href>Progressive <br>Events</a></li>
			<li><a data-toggle="tab" href>Previous <br>Events</a></li>
			<li><a data-toggle="tab" href>Calendar <br>of Events</a></li>
			
			<li><a data-toggle="tab" href>Discussion</a></li>
			<li><a data-toggle="tab" href>Mail</a></li>
		</ul>
	</div>

	<div class="row">
		<div class="col-sm-8">

			<div data-ng-view></div>

		</div>
		<div class="hidden-xs col-sm-4">
			<div class="sidebar" ng-controller="SidebarController">
				<div class="group-about">
					<h4 class="gray">About</h4>
					<p>
						<i class="gray glyphicon <?=$gig_group->privacy == 'public' ? 'glyphicon-globe' : 'glyphicon-lock'?>"></i> 
						<?=$gig_group->privacy == 'public' ? 'Public' : 'Private' ?> Group
					</p>

					
					@if( $gig_group->description )
						<div class="group-description">
						@if( str_word_count($gig_group->description) > 100 )
							<div class="group-description-excerpt" ng-hide="seeMore">
								<?=Str::words($gig_group->description,100)?>
								
							</div>
							<dv class="group-description-full" ng-show="seeMore">
								<?=$gig_group->description?>	
							</div>

							<a class="see-more" href ng-click="seeMore =  true" ng-hide="seeMore">See More</a>
							<p ng-show="seeMore"><a class="show-less" href ng-click="seeMore = false">Show Less</a></p>
							
						@else
							<dv class="group-description-full">
								<?=$gig_group->description?>	
							</div>
						@endif
						</div>
					@else

						@if( $the_member && $the_member->is_admin )
							<div class="group-description">
								<p ng-if="!gig_group.description">Tell people what this group is about.</p>
							</div>
							<div class="add-description">
								<form class="add-description-form" ng-show="showDescriptionForm">
									<div class="form-group">
										<textarea class="form-control" name="description" ng-model="gigGroup.description"></textarea>
									</div>
									<div>
										<button class="btn btn-primary btn-xs">Save</button>
										<a class="cancel-group-description pDefault" style="margin:5px " href="javascript:;" ng-click="showDescriptionForm = false">Cancel</a>
									</div>
								</form>
								<a class="show-description-form" ng-hide="showDescriptionForm" ng-click="showDescriptionForm = true" href="javascript:;">Add description</a>
							</div>
						@else
							<p class="group-description gray">No group description</p>
						@endif
					@endif
				</div>

				<button class="" ng-click="clicked = 23">Click</button>
				<button class="" ng-click="check()">Check</button>
			</div>
		</div>
	</div>

@endsection