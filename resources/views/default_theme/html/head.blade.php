<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@section('title') GigHubApp @show</title>
	
	@section('stylesheets')
		<?=HTML::style('css/bootstrap-united.min.css')?>
		<?=HTML::style('css/font-awesome.min.css')?>
		<?=HTML::style('css/plugins/bsbox/bsbox.css')?>
		<?=HTML::style('css/plugins/typeahead/typeahead.js-bootstrap.css')?>
		<?=HTML::style('css/library/gighubapp/gha-framework.css')?>
	@show

	@section('scripts')

		<?=HTML::script('js/library/jquery/jquery-1.10.1.min.js')?>
		<?=HTML::script('js/plugins/typeahead/typeahead.jquery.js')?>
		<?=HTML::script('js/library/angular/angular.min.js')?>
		<?=HTML::script('js/library/bootstrap/bootstrap.min.js')?>
		<?=HTML::script('js/plugins/bsbox/bsbox2-bundle.js')?>
		<?=HTML::script('js/plugins/gha/gha-framework.js')?>
		<script>
		var url = {
			_api: '<?=URL::route('api.index')?>',
			home:'<?=URL::to('/')?>',
			upload:{
				image:{
					user_profile_img:'<?=Config::get('gha.upload.image.user_profile_img.base_url')?>',
					event_poster: '<?=Config::get('gha.upload.image.event_poster.base_url')?>',
					event_description_img: '<?=Config::get('gha.upload.image.event_description_img.base_url')?>',
					group_cover_img: '<?=Config::get('gha.upload.image.group_cover_img.base_url')?>',
					group_discussion_img: '<?=Config::get('gha.upload.image.group_discussion_img.base_url')?>',
				}
			},
			user: '<?=Auth::check() ? URL::route('user_show', Auth::user()->slug) : ''?>',
			image:{
				profile_img: '<?=Config::get('gha.upload.image.user_profile_img.base_url')?>'
			}
		}
		<?php if( Auth::check() ): ?>
			GlobalStorage.store('me', <?=Auth::user()?> );
		<?php endif; ?>
		</script>
	@show
</head>