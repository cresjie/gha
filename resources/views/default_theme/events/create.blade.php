@extends('layout.2columns-with-ads')

@section('stylesheets')
	@parent
	<?=HTML::style('css/library/datetime/bootstrap-datetimepicker.min.css')?>

	<?=HTML::style('css/library/froala/froala_editor.min.css')?>
	<?=HTML::style('css/library/froala/froala_content.min.css')?>

	
	
	<?=HTML::style('css/customcss/events/create.css')?>
@endsection
	
@section('scripts')
	@parent
	<?=HTML::script('js/library/datetime/moment.js')?>
	<?=HTML::script('js/library/datetime/bootstrap-datetimepicker.min.js')?>

	<?=HTML::script('js/library/FileWizard/FileWizard-bundle.js')?>
	<?=HTML::script('js/library/labs-validator/labsValidator.js')?>

	<?=HTML::script('js/library/froala/froala_editor.min.js')?>
	<?=HTML::script('js/plugins/froala/colors.min.js')?>
	<?=HTML::script('js/plugins/froala/font_size.min.js')?>

	<?=HTML::script('js/library/sortable/Sortable.min.js')?>
	<?=HTML::script('js/library/sortable/ng-sortable.js')?>

	<?=HTML::script('js/customjs/gig_events/create.js')?>
	<script>
		<?php $account = $user->usertype->account ;?>

		GlobalStorage.store('usertype',<?=$account?>);
		GlobalStorage.store('organizer_limit', <?=Config::get('gha.usertype.gig_organizer.' . $account->code)?:Config::get('gha.usertype.gig_organizer.standard')?>);
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
	<script>
		function mapInit(){
			var map = new google.maps.Map(document.getElementById("map_canvas"),{
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				center: new google.maps.LatLng(-34.397, 150.644),
				zoom:5
			});

			var marker = new google.maps.Marker({map:map});

			var searchBox = new google.maps.places.SearchBox( document.getElementById('location') );
		    
		    google.maps.event.addListener(searchBox,'places_changed',function(){
		    	var place = searchBox.getPlaces()[0];
		    	
		    	marker.setPosition(place.geometry.location);
		    	map.setCenter(place.geometry.location);
		    	map.setZoom(18);

		    	for(var key in place.address_components){
		    		var component = place.address_components[key];
		    		var types = component.types;
		    		for(var k in types){
		    			var loc = ['country','locality','administrative_area_level_1'];
		    			var i = $.inArray(types[k],loc);
		    			if(i >= 0){
		    				$('#location_' + loc[i]).val(component.long_name);
		    			}
		    		}
		    	}

		    	$('[name="coordinates"]').prop('value', JSON.stringify( place.geometry.location ) );
		    	$('.location-info').trigger('input');
		    });
		}
		google.maps.event.addDomListener(window, 'load', mapInit);

	</script>
@endsection


@section('content')
	<div class="h-spacer-10"></div>
	<div ng-controller="GigEventController">
		<?=Form::open(['method' => 'post', 'id' => 'event-form'])?>
		<h2 class="section-header"><span>1</span> Event Details</h2>
		
		<div class="form-group" ng-class="{'has-error': error.gig_event.title}">
			<label class="control-label" for="event_title">Event Title:<span class="required">*</span></label>
			<input type="text" class="form-control" name="title"  ng-model="gig_event.title" ng-blur="setSlug($event, gig_event.title)" validator-required>
			<p class="help-block" ng-repeat="msg in error.gig_event.title">@{{msg}}</p>
		</div>

		<div class="slug-wrapper form-group" ng-class="{'has-error': error.gig_event.slug}" ng-controller="SlugController">
			<label class="control-label">Link:</label>
			<div>
				<span><?=URL::route('events.index')?>/@{{gig_event.id ? gig_event.id : '...'}}-</span><span ng-hide="slug_editing">@{{slug}}</span>
				<input type="text" size="20" name="group_slug" class="slug-input" ng-model="slug" ng-show="slug_editing" ng-focus-this="slug_editing" ng-blur="doneEdit()" ng-keydown="changeWidth($event)">
				<a href="#" class="btn-edit" ng-click="slugEdit()" ng-hide="slug_editing"><i class="glyphicon glyphicon-pencil"></i></a>
			</div>
			<p class="help-block" ng-repeat="msg in error.gig_event.slug">@{{msg}}</p>
		</div>
		<!--
		<div class="form-group">
			<label class="control-label">Slogan or Cliche:</label>
			<input type="text" name="slogan" ng-model="gig_event.slogan" class="form-control">
		</div>
		-->
		<div class="form-group">
			<label class="control-label">Category:</label>
			<div class="category-list">

				@foreach($event_categories as $category)
				<div class="checkbox-inline flat-checkbox">
					<label><input type="checkbox" name="event_category" value="<?=$category->id?>" ng-click="updateEventCategories(<?=$category->id?>)"><i class="fa checkbox-icon checkbox-icon-primary"></i><?=$category->name?></label>
				</div>
				@endforeach

			</div>
			
		</div>

		

		<div class="form-group" ng-hide="hasEventCategory(6)">
			<label for="event_location">Location:</label>
			<div class="row">
				<div class="col-sm-8">
					<input type="text" id="location" name="location" ng-model="gig_event.location" class="form-control" placeholder="Venue">
					<br>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" class="form-control location-info" placeholder="Locality" id="location_locality" name="location_locality" ng-model="location.locality" readonly>
							</div>
							<div class="form-group">
								<input type="text" class="form-control location-info" placeholder="Administrative area" id="location_administrative_area_level_1" name="location_administrative_area_level_1" ng-model="location.administrative_area_level_1" readonly>
							</div>
							<div class="form-group">
								<input type="text" class="form-control location-info" placeholder="Country" id="location_country" name="location_country" ng-model="location.country" readonly>
							</div>
							
							
							<input type="hidden" class="form-control location-info" id="location_coordinates"name="location_coordinates" ng-model="location.coordinates">

						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div id="map_canvas" style="height:200px"></div>
				</div>
			</div>
		</div>

		<div class="form-group" ng-show="hasEventCategory(6)">

			<label class="control-label">Timezone</label>
			<div class="row">
				<div class="col-sm-8">
					<select name="timezone" class="form-control" ng-model="timezone">
						<?php 
							$countries = App\Helpers\Directory\Country::lists();
							foreach($countries as $code => $country):
								$tz = App\Helpers\Directory\DateTimeZone::byCountryAbbr($code);
						?>
							<option value="<?=$tz?>"><?=$country?> - <?=$tz?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			
			
		</div>
		<div class="form-group">
			<label>Event Poster:</label>
			<div class="row">
				<div class="col-sm-12">
					<div id="filewizard" class="fileward-wrapper">
						<div class="img-container">
							<img class="img-responsive" src="">
						</div>
						<div class="drop-handle-wrapper">
							<div class="drop-handle  transition-500">
								<h4 class="drop-title">Drag & drop image</h4>
								<div class="progress progress-striped" style="margin-top:10px; display:none ">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"> <span class="sr-only"></span> </div> 
								</div>
							</div>
						</div>
						
						
					</div>
				</div>
				
			</div>
		</div>

		
		<div class="successive-dates-container">
			<div class="row successive-dates-wrapper" ng-repeat="date in successiveDates">
				<div class="col-xs-12">
					<div style="position:relative">
						<div class="row">
							<div class="col-sm-6">
						
								<div class="form-group" ng-class="{'has-error': error.successiveDates[$index].start}">
									<label class="control-label hidden-sm hidden-md hidden-lg">Starts:<span class="required">*</span></label>
									<input type="text" class="form-control datetime event-start event-start-@{{$index}}" event-start="@{{$index}}" placeholder="Date and Time" ng-model="date.start">
									<p class="help-block"  ng-repeat="msg in error.successiveDates[$index].start">@{{msg}}</p>
								</div>
								
							</div>
							<div class="col-sm-6">
								
								<div class="form-group" ng-class="{'has-error': error.successiveDates[$index].end}">
									<label class="control-label hidden-sm hidden-md hidden-lg">Ends:<span class="required">*</span></label>
									<input type="text" class="form-control datetime event-end event-end-@{{$index}}" event-end="@{{$index}}" placeholder="Date and Time" ng-model="date.end">
									<p class="help-block"  ng-repeat="msg in error.successiveDates[$index].end">@{{msg}}</p>
								</div>
								
							</div>
						</div>
						<a class="remove-successive" href ng-click="successive.remove(date)">×</a>
					</div>
				</div>
				
			</div>
			<div class="form-group">
				<button type="button" class="btn btn-bg1" ng-click="successive.add()"><i class="glyphicon glyphicon-plus"></i> Add date</button>
			</div>
			
		</div>
		

		<div class="form-group" ng-class="{'has-error': error.gig_event.description}">
			<label class="control-label">Description:<span class="required">*</span></label>
			<textarea id="gig_event_description" name="description" ng-model="gig_event.description" class="form-control"></textarea>
			<p class="help-block" ng-repeat="msg in error.gig_event.description">@{{msg}}</p>
		</div>

		<h2 class="section-header"><span>2</span> Planning Team <small>(Max: @{{organizerLimit}} members )</small></h2>

		<div class="organizer-box">
			<ul class="pad-0 organizer-list">
				<li class="dropdown" ng-repeat="organizer in organizers">
					<div class="text-ellipsis organizer">
						@{{organizer.first_name}} @{{organizer.last_name}}
						
						<a class="actions" data-toggle="dropdown" href="#" aria-expanded="false"><b class="caret"></b></a>
						<a class="actions" ng-click="removeOrganizer(organizer)" href=""><i class="fa fa-times"></i></a>
						<ul class="dropdown-menu">
							<li>
								<label class="checkbox"><input type="checkbox" ng-model="organizer.is_admin"> Admin priviledge</label>
							</li>
						</ul>
					</div>
				</li>
				<li class="organizer-search-wrapper">
					<input type="text" class="transparent-input organizer-search" placeholder="Search contact" ng-hide="organizers.length >= organizerLimit">
				</li>
			</ul>

			
		</div>

		
		<!--
		<div class="form-group">
			<div class="input-group">
				<input type="text" class="form-control input-sm">
				<div class="input-group-btn">
					<a class="btn btn-success btn-sm">Add</a>
				</div>
			</div>
		</div>
		-->
		<h2 class="section-header" ng-init="event.requisite = 'ticket'"><span>3</span> Participation Requisite</h2>

		<div class="radio-inline flat-radio">
			<label><input type="radio" name="requisite" ng-model="gig_event.requisite" value="ticket" ng-checked="true"><i class="fa radio-icon radio-icon-primary"></i>Ticket</label>
		</div>
		<div class="radio-inline flat-radio">
			<label><input type="radio" name="requisite" ng-model="gig_event.requisite" value="rsvp"><i class="fa radio-icon radio-icon-primary"></i>RSVP</label>
		</div>
		<div class="radio-inline flat-radio">
			<label><input type="radio" name="requisite" ng-model="gig_event.requisite" value="none"><i class="fa radio-icon radio-icon-primary"></i>None</label>
		</div>

		<div ng-show="gig_event.requisite == 'ticket'">
			<div class="form-group">
				<label>Currency:</label>
				<select class="form-control" ng-model="ticketCurrency" style="max-width:300px">
					<?php foreach(App\Helpers\Directory\Currency::codes() as $code => $name): ?>
						<option value="<?=$code?>"><?=$name?>  (<?=$code?>)</option>
					<?php endforeach;?>
				</select>
			</div>
			<div class="ticket-table-responsive">
				<div class="ticket-table">
					<div class="row ticket-thead">
						<div class="col-xs-1">
						</div>
						<div class="col-xs-2">
							<label>Ticket Name<span class="required">*</span></label>
						</div>
						<div class="col-xs-3">
							<label>Sales End</label>
						</div>
						<div class="col-xs-2">
							<label>Price<span class="required">*</span></label>
						</div>
						<div class="col-xs-2">
							<label>Stock<span class="required">*</span></label>
						</div>
						<div class="col-xs-2">
							<label>Action</label>
						</div>
					</div>
					<div class="ticket-body" ng-sortable="{handle:'.handle'}">
						<div class="ticket-row-wrapper" ng-repeat="ticket in tickets.data">
							<div class="row" >
								<div class="col-xs-1 text-center">
									<div class="handle"><i class="fa fa-bars handle-bars"></i></div>
								</div>
								<div class="col-xs-2">
									<div class="form-group" ng-class="{'has-error': error.tickets[$index].name}">
										<input type="text" class="form-control" placeholder="Ticket Name" ng-model="ticket.name">
										<p class="help-block" ng-repeat="msg in error.tickets[$index].name">@{{msg}}</p>
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group" ng-class="{'has-error': error.tickets[$index].sales_end}">
										<input type="text" class="form-control sales-end" placeholder="Date and Time" ng-model="ticket.sales_end">
										<p class="help-block" ng-repeat="msg in error.tickets[$index].sales_end">@{{msg}}</p>
									</div>
									
								</div>
								<div class="col-xs-2">
									<div class="form-group" ng-class="{'has-error': error.tickets[$index].price}">
										<input type="number" size="2" min="0" step=".1" class="form-control" placeholder="@{{ticket.type == 'free' ? 'Free' : ticketCurrency}}" ng-model="ticket.price" ng-disabled="ticket.type == 'free'">
										<p class="help-block" ng-repeat="msg in error.tickets[$index].price">@{{msg}}</p>
									</div>
									
								</div>
								<div class="col-xs-2">
									<div class="form-group" ng-class="{'has-error': error.tickets[$index].stock}">
										<input type="number" class="form-control" min="0" placeholder="Stock" ng-model="ticket.stock">
										<p class="help-block" ng-repeat="msg in error.tickets[$index].stock">@{{msg}}</p>
									</div>
								</div>
								<div class="col-xs-2 ticket-actions">
									
										<a class="ticket-btn" href="#ticket-setting-@{{$index}}" data-toggle="collapse"><i class="fa fa-cog"></i></a>
										<a class="ticket-btn" href ng-click="tickets.remove(ticket)"> <i class="fa fa-times"></i></a>
									
									
								</div>
							</div>
							<div id="ticket-setting-@{{$index}}" class="ticket-setting row collapse">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group" ng-class="{'has-error': error.tickets[$index].sales_start}">
												<label class="control-label">Sales Start</label>
												<input type="text" name="" class="form-control sales-start" placeholder="Date Time" ng-model="ticket.sales_start">
												<p class="help-block" ng-repeat="msg in error.tickets[$index].sales_start">@{{msg}}</p>
											</div>
											<div class="form-group" ng-class="{'has-error': error.tickets[$index].description}">
												<label class="control-label">Ticket Description:</label>
												<textarea class="form-control" style="height:70px" ng-model="ticket.description"></textarea>
												<p class="help-block" ng-repeat="msg in error.tickets[$index].description">@{{msg}}</p>
											</div>
										</div>
										<div class="col-sm-6">
											<label>Tickets allowed per order</label>
											<div class="row">
												<div class="col-sm-6 pad-0">
													<div class="form-group" ng-class="{'has-error': error.tickets[$index].minimum}">
														<!-- <label class="control-label">Minimum</label> -->
														<input type="number" name="minimum" min="1" class="form-control"  ng-model="ticket.minimum" ng-init="ticket.minimum = 1">
														<div><small><i>Minimum</i></small></div>
														<p class="help-block" ng-repeat="msg in error.tickets[$index].minimum">@{{msg}}</p>
													</div>
												</div>
												<div class="col-sm-6" style="padding-top:0">
													<div class="form-group" ng-class="{'has-error': error.tickets[$index].maximum}">
														<!-- <label class="control-label">Maximum</label> -->
														<input type="number" name="maximum" class="form-control" min="1"  ng-model="ticket.maximum" >
														<div><small><i>Maximum</i></small></div>
														<p class="help-block" ng-repeat="msg in error.tickets[$index].maximum">@{{msg}}</p>
													</div>
												</div>
											</div>
											
											
										</div>
									</div>
								</div>
							</div>
						</div>
							

					</div>
					
				</div>
				
			</div>



			<button type="button" class="btn btn-bg1" ng-click="tickets.add({type:'free'})"><i class="glyphicon glyphicon-plus"></i> Add Free Ticket</button>
			<button type="button" class="btn btn-bg1" ng-click="tickets.add({type:'paid'})"><i class="glyphicon glyphicon-plus"></i> Add Paid Ticket</button>
		</div>

		<div ng-show="gig_event.requisite == 'rsvp'" class="form-horizontal">
			<div class="">
				<div class="form-group" ng-class="{'has-error': error.rsvp.limit}">
					<label class="control-label col-sm-3">Total number of attendees<span class="required">*</span></label>
					<div class="col-sm-2">
						<input type="number" class="form-control" min="1" ng-model="rsvp.limit">
						<p class="help-block" ng-repeat="msg in error.rsvp.limit">@{{msg}}</p>
					</div>	

					
				</div>
			</div>
			

			<div class="form-group">
				<label class="control-label col-sm-3">Allow guest?</label>
				<label class="radio-inline flat-radio"><input type="radio" name="allow_guest" ng-model="allowGuest" value="1" ng-model="allowGuest"><i class="fa radio-icon radio-icon-primary"></i> Yes</label>
				<label class="radio-inline flat-radio"><input type="radio" name="allow_guest" ng-model="allowGuest" value="0" ng-model="allowGuest" ng-change="rsvp.maximum_guest = 0"><i class="fa radio-icon radio-icon-primary"></i> No</label>
			</div>
			<div class="form-group" ng-show="allowGuest == 1" ng-class="{'has-error': error.rsvp.maximum_guest}">
				<label class="control-label col-sm-3">Limit guest</label>
				<div class="col-sm-2">
					<input type="number" class="form-control" min="0" ng-model="rsvp.maximum_guest">
					<p class="help-block" ng-repeat="msg in error.rsvp.maximum_guest">@{{msg}}</p>
				</div>
				
			</div>
			<div class="form-group" ng-class="{'has-error': error.rsvp.display_remaining}">
				<label class="control-label col-sm-3"></label>
				<label class="checkbox-inline flat-checkbox">
					<input type="checkbox" name="display_remaining" ng-model="rsvp.display_remaining"><i class="fa checkbox-icon checkbox-icon-primary"></i> Display remaining spots
					<p class="help-block" ng-repeat="msg in error.rsvp.display_remaining">@{{msg}}</p>
				</label>
				
				
			</div>
		</div>


		<h2 class="section-header"><span>4</span> Additional Settings</h2>
		<div class="form-group" ng-init="gig_event.privacy = 'public'">
			<label>Privacy Setting:</label>
			<div class="radio flat-radio">
				<label><input checked="checked" name="privacy" type="radio" value="public" ng-model="gig_event.privacy"><i class="fa radio-icon radio-icon-primary"></i>Public Page: <small>include this event in GigHubApp and Search engines</small></label>
			</div>
			<div class="radio flat-radio">
				<label><input name="privacy" type="radio" value="private" ng-model="gig_event.privacy"><i class="fa radio-icon radio-icon-primary"></i>Private Page: <small>do not post this event publicly</small></label>
			</div>
		</div>
		<div class="form-group" ng-show="event.privacy == 'private'">
			<label>Private Description</label>
			<textarea class="form-control" name="private_description" ng-model="gig_event.private_description"></textarea>
		</div>
		
		<div>
			<button type="button" class="btn btn-primary submit-btn" ng-click="publish()">Publish</button>
			<button type="button" class="btn btn-warning submit-btn" ng-click="draft()">Save as Draft</button>
		</div>
		<button type="button" ng-click="check()">Check</button>
		<?=Form::close()?>
	</div>
	
@endsection