@extends('layout.2columns-with-ads')

@section('scripts')
	@parent
	<?=HTML::script('js/customjs/gig_group/create.js')?>
	<script>
		$.extend(url,{
			api: {
				search: {
					users: '<?=URL::route('api.search.users')?>'
				},
				slug: '<?=URL::route('api.slug.index')?>',
				gig_group: '<?=URL::route('api.gig_group.index')?>',
				gig_group_members: '<?=URL::route('api.gig_group_members.index')?>'
			},
			
		});
	</script>
@endsection

@section('stylesheets')
	@parent
	<?=HTML::style('css/gig_group/create.css')?>
@endsection
@section('content')
	<div class="page-header">
		<h1>Create Group </h1>
	</div>

	<div role="form-container" ng-controller="GigGroupController">

		<div class="form-group" ng-class="{'has-error': error_msg.name}">
			<label class="control-label">Group Name:<span class="required">*</span></label>
			<input type="text" name="name" class="form-control" style="max-width:350px" ng-model="group.name" ng-blur="setSlug($event, group.name)">
			<p class="help-block" ng-repeat="msg in error_msg.name">@{{msg}}</p>
		</div>

		<div class="slug-wrapper form-group" ng-controller="SlugController">
			<label>Link:</label>
			<div>
				<span><?=URL::route('gig_group.index')?>/</span><span ng-hide="slug_editing">@{{slug}}</span>
				<input type="text" name="group_slug" ng-model="slug" ng-show="slug_editing" ng-focus-this="slug_editing" ng-blur="doneEdit()">
				<a href="#" class="btn-edit" ng-click="slugEdit()" ng-hide="slug_editing"><i class="glyphicon glyphicon-pencil"></i></a>
			</div>
			
		</div>

		<div class="">
			<div class="form-group" style="max-width:350px">
				<label class="control-label">Slogan:</label>
				<input type="text" name="slogan" ng-model="group.slogan" class="form-control">
			</div>
		</div>

		

		
		<div class="form-group">
			<label class="control-label">Description:</label>
			<textarea class="form-control" ng-model="group.description" cols="50" rows="10"></textarea>
		</div>

		<div class="form-group">
			<label>Members</label>
			<div class="member-list-wrapper">
				<ul class="list-inline added-member-list">
					<li ng-repeat="member in groupMembers">
						<div>
							@{{member.first_name}} @{{member.last_name}} <button type="button" class="close" ng-click="removeMember(member)">x</button>
						</div>
					</li>
				</ul>
			</div>
			<div class="input-group" style="max-width:350px">
				<input type="text" class="form-control input-sm" id="member-input">
				<span class="input-group-btn">
					<a class="btn btn-sm btn-success pDefault add-member-btn" ng-click="addMember()">Add</a>
				</span>
			</div>
			
		</div>


		<div class="form-group">
			<label class="control-label">Privacy Setting</label>
			<div class="radio">
				<label><input type="radio" value="public" name="privacy" ng-model="group.privacy">Public Group: <small>index this group in searches</small></label>
			</div>
			<div class="radio">
				<label><input type="radio" value="private" name="privacy" ng-model="group.privacy">Private Group: <small>only members or anyone with the link can view the group page</small></label>
			</div>
		</div>

		<button class="btn btn-primary" ng-click="submit()">Submit</button>
		
	</div>



@endsection