@extends('layout.2columns-with-ads')

@section('scripts')
	@parent
	<?=HTML::script('js/library/angular/angular-route.min.js')?>
	<?=HTML::script('js/customjs/settings/index.js')?>
@endsection
@section('content')
	<div >
			<h1>Account Setting</h1>
	</div>
	<div class="tabs-container" ng-controller="TabController">
		<ul class="nav nav-tabs nav-tabs-flat text-center">
			<li ng-class="{active: isCurrentTab('/')}">
				<a  href="#/">Basic Info</a>
			</li>
			<li ng-class="{active: isCurrentTab('/password-setting')}">
				<a  href="#/password-setting">Password</a>
			</li>
		</ul>
	</div>

	<div class="tab-content tab-content-offset-20">
		<div ng-view></div>
	</div>

@endsection