var templates = {
		suggestion_organizer: function(user){
			return '<div class="row suggest-organizer">'+
						'<div class="col-sm-2 col"><img class="img-responsive" src="'+url.image.profile_img+'/w50/'+user.profile_img+'"></div>'+
						'<div class="col-sm-10 col"><strong>'+user.first_name+' '+user.last_name+'</strong></div>'+
					'</div>';
		}
	};

var savingState = function(saving){
	$('.submit-btn').prop('disabled', saving);
	saving ? $('.content-spinner').fadeIn() : $('.content-spinner').fadeOut();
}

var app = angular.module('App',['GHAFramework','ng-sortable']);

app.controller('GigEventController',function($scope, $http, $rootScope, $timeout,ErrorService, BSBoxService){
	$scope.onlineEventId = 6;
	$scope.eventCategories = [],
	$scope.organizers = [];
	$scope.successiveDates = [{}];
	$scope.organizerLimit = GlobalStorage.get('organizer_limit');
	$scope.error = {
		successiveDates: [],
		tickets: []
	};
	$scope.rsvp = {
		limit: 100,
		display_remaining: true
	};
	$scope.gig_event = {
		requisite: 'ticket'
	};


	$scope.ticketCurrency = 'USD';
	$scope.allowGuest = 0;

	//$submitBtn = angular.element('.submit-btn');

	$scope.setSlug = function(e, val){
		$http.post(url._api+'/slug',{q: val})
			.success(function(res){
				if( !$scope.gig_event )
					$scope.gig_event = {};

				$scope.gig_event.slug = res.slug;
				$rootScope.$emit('slug.set', res.slug);
			});
	}

	$rootScope.$on('slug.change', $scope.setSlug);

	$scope.updateEventCategories = function(id){
		var i = $scope.eventCategories.indexOf(id);
		if( i > -1 )
			$scope.eventCategories.splice(i, 1);
		else
			$scope.eventCategories.push(id);
	}

	$scope.hasEventCategory = function(id){
		return $scope.eventCategories.indexOf(id) >= 0 ? true :false;
	}

	$scope.removeOrganizer = function(organizer){
		var i = $scope.organizers.indexOf(organizer);
		$scope.organizers.splice(i, 1);
	}

	$scope.test = function(){
		alert()
	}
	$scope.check = function(){
		console.log($scope);

	}

	

	$scope.successive = {
		add: function(){
			$scope.successiveDates.push({});
		},
		remove: function(date){
			if( $scope.successiveDates.length > 1){
				var i = $scope.successiveDates.indexOf(date);
				
				if(date.id){
					$http.delete(url._api+'/'+date.id)
						.success(function(res){
							if(res.success)
								$scope.successiveDates.splice(i,1);
						})
						.error(function(res){_ajaxError(res);});
				}else{
					$scope.successiveDates.splice(i,1);
				}

				
			}else{
				bsbox.alert('There should be atleast 1 event start and end.');
			}
			
		}
	}

	var addOrganizer = function(e, user){
		$scope.organizers.push(user);
		angular.element(this).typeahead('val','');
		$scope.$apply();
	}

	$scope.tickets = {
		data:[],
		add: function(ticket){
			$scope.tickets.data.push(ticket);
		},

		remove: function(ticket){
			var i = $scope.tickets.data.indexOf(ticket);
			$scope.tickets.data.splice(i, 1);
		}
	}

	$scope.publish = function(){
		if(!$scope.gig_event)
			$scope.gig_event = {};
		$scope.gig_event.publish = true;

		saveEvent();
	}

	$scope.draft = function(){
		if(!$scope.gig_event)
			$scope.gig_event = {};
		$scope.gig_event.publish = false;

		saveEvent();
	}

	var saveEvent = function(){
		angular.element('#gig_event_description').trigger('input');
		savingState(true);
		$scope.error.gig_event = {}; //reset error

		if($scope.gig_event && $scope.gig_event.id){
			console.log('gig_event.updating');
			$http.put(url._api+'/events/'+$scope.gig_event.id, $scope.gig_event)
				.success(function(res){
					if(res.success){
						angular.extend($scope.gig_event, res.data);
						$scope.$emit('gig_event.saved');
					}else{
						savingState(false);
						$scope.error.gig_event = res.error_msg;
						BSBoxService.notif.warning('Invalid input.');
						ErrorService.locateField();
					}
				})
				.error(function(res){_ajaxError(res);});
		}else if($scope.gig_event){
			console.log('gig_event.creating');
			$http.post(url._api+'/events', $scope.gig_event)
				.success(function(res){
					if(res.success){
						angular.extend($scope.gig_event, res.data);
						$scope.$emit('gig_event.saved');
					}else{
						console.log(res);
						$scope.error.gig_event = res.error_msg;
						savingState(false);
						BSBoxService.notif.warning('Invalid input.');
						ErrorService.locateField();
					}
				})
				.error(function(res){_ajaxError(res);});
		}
	}

	$scope.$on('gig_event.saved', function(){
		console.log('processing successive dates');

		var savedDates = 0;
		$scope.error.successiveDates = []; //resets the error index

		var dateSaved = function(){
			savedDates++;
			if(savedDates >= $scope.successiveDates.length)
				$scope.$emit('successiveDates.saved');
		}
		
		for(var i =0; i < $scope.successiveDates.length; i++) {
			setTimeout(function(_i){
				var date = $scope.successiveDates[_i];
				date.event_id = $scope.gig_event.id;

				if( $scope.hasEventCategory($scope.onlineEventId) )
					date.timezone =   $scope.timezone ;
				else if( $scope.location && $scope.location.country )
					date.country = $scope.location.country;

				date.timezone = "Asia/Manila";

				if( date.id ){
					console.log('updating successive dates');
					$http.put(url._api+'/event-dates/'+date.id, date)
						.success(function(res){ 
							console.log(res);
							if(res.success){
								angular.extend(date,{id: res.data.id, timezone: res.data.timezone});
								dateSaved();
							}else{
								savingState(false);
								$scope.error.successiveDates[_i] = res.error_msg;

								BSBoxService.notif.warning('Invalid input on event date(s).');
								

							}
						})
						.error(function(res){_ajaxError(res);});
				}else{
					console.log('creating successive dates');
					$http.post(url._api+'/event-dates',date)
						.success(function(res){ console.log(res);
							if(res.success){
								angular.extend(date,{id: res.data.id, timezone: res.data.timezone});

								dateSaved();
							}else{
								savingState(false);
								$scope.error.successiveDates[_i] = res.error_msg;

								BSBoxService.notif.warning('Invalid input on event date(s).');
								ErrorService.locateField();
								
							}
						})
						.error(function(res){_ajaxError(res)});
				}
				
			},0,i);
		}
	}); //end $scope.$on gig_event.saved
	
	$scope.$on('successiveDates.saved', function(){
		console.log('processing geolocation');
		if( $scope.location ){
			if($scope.location.id){
				$http.put(url._api+'/geolocation/events/'+$scope.location.id, $scope.location)
					.success(function(res){
						if(res.success){
							angular.extend($scope.location, res.data);
							$scope.$emit('geolocation.saved');
						}else{
							savingState(false);
							BSBoxService.notif.warning('Unable to saved geolocation.');
						}
					})
					.error(function(res){_ajaxError(res)});
			}else{
				$scope.location.trackable_id = $scope.gig_event.id;
				$http.post(url._api+'/geolocation/events', $scope.location)
					.success(function(res){
						if(res.success){
							angular.extend($scope.location, res.data);
							$scope.$emit('geolocation.saved');
						}else{
							savingState(false);
							BSBoxService.notif.warning('Unable to saved geolocation.');
						}
					})
					.error(function(res){_ajaxError(res)});
			}
		}else{
			$scope.$emit('geolocation.saved');
		}
	});

	$scope.$on('geolocation.saved', function(){
		console.log('processing organizer');
		var savedOrganizer = 0;
		var organizerSaved = function(){
			savedOrganizer++;
			if(savedOrganizer >= $scope.organizers.length){
				$scope.$emit('organizers.saved');
			}

		}
		if( $scope.organizers.length){
			for(var i =0; i < $scope.organizers.length; i++ ) {
				setTimeout(function(user){
					console.log(user);
					if(!user.organizer_details){
						$http.post(url._api+'/event-organizers',{event_id: $scope.gig_event.id, user_id: user.id, is_admin: user.is_admin})
							.success(function(res){
								console.log(res);
								if(res.success){
									angular.extend(user,{organizer_details: res.data});
									organizerSaved();
								}else{
									BSBoxService.notif.warning('Invalid input on organizer.');
									ErrorService.locateField();
									savingState(false);
								}
							})
							.error(function(res){_ajaxError(res)});
					}else{
						organizerSaved();
					}
				},0,$scope.organizers[i]);
				
				
			}
		}else{
			$scope.$emit('organizers.saved');
		}
		
	});

	var saveTickets = function(){
		console.log('processing tickets');

		$scope.error.tickets = [];
		var savedTickets = 0;

		var ticketSaved = function(){
			savedTickets++;
			if(savedTickets >= $scope.tickets.data.length)
				$scope.$emit('requisite.saved');
		}		

		if( $scope.tickets.data.length ){
			for(var i =0; i < $scope.tickets.data.length; i++){
				setTimeout(function(_i){ 
					
					var ticket = $scope.tickets.data[_i];
					if( ticket.id ){
						ticket.sort_number = _i+1;
						ticket.currency = $scope.ticketCurrency;
						$http.put(url._api+'/event-tickets/'+ ticket.id, ticket)
							.success(function(res){
								if(res.success){
									angular.extend(ticket, res.data);
									ticketSaved();
								}else{
									$scope.error.tickets[_i] = res.error_msg;
									angular.element('.ticket-row-wrapper .has-error').parents('.ticket-setting').collapse('show');
									BSBoxService.notif.warning('Invalid input on ticket.');
									ErrorService.locateField();
									savingState(false);
								}
								
							})
							.error(function(res){_ajaxError(res)});
					}else{
						ticket.event_id = $scope.gig_event.id;
						ticket.sort_number = _i+1;
						$http.post(url._api+'/event-tickets', ticket)
							.success(function(res){
								console.log(res);
								if(res.success){
									angular.extend(ticket, res.data);
									ticketSaved();
								}else{
									$scope.error.tickets[_i] = res.error_msg;
									angular.element('.ticket-row-wrapper .has-error').parents('.ticket-setting').collapse('show');
									BSBoxService.notif.warning('Invalid input on ticket.');
									ErrorService.locateField();
									savingState(false);
								}
							})
							.error(function(res){_ajaxError(res)});
						
					}
					console.log(ticket.sort_number);
				},0, i);
			}
		}else{
			$scope.$emit('requisite.saved');
		}
	}

	var saveRsvp = function(){
		console.log('processing rsvp');
		$scope.error.rsvp = {}; //resets
		if( $scope.rsvp.event_id ){
			$http.put(url._api+'/event-rsvp', $scope.rsvp)
				.success(function(res){
					if(res.success){
						angular.extend($scope.rsvp, res.data);
						$scope.$emit('requisite.saved');
					}else{
						$scope.error.rsvp = res.error_msg;
						BSBoxService.notif.warning('Invalid input on participation requisite.');
						ErrorService.locateField();
						savingState(false);
					}
				})
				.error(function(res){_ajaxError(res)});
		}else{
			$scope.rsvp.event_id = $scope.gig_event.id;
			$http.post(url._api+'/event-rsvp', $scope.rsvp)
				.success(function(res){
					if(res.success){
						angular.extend($scope.rsvp, res.data);
						$scope.$emit('requisite.saved');
					}else{
						$scope.error.rsvp = res.error_msg;
						BSBoxService.notif.warning('Invalid input on participation requisite.');
						ErrorService.locateField();
						savingState(false);
					}
				})
				.error(function(res){_ajaxError(res)});
		}
	}

	$scope.$on('organizers.saved', function(){
		
		if( $scope.gig_event.requisite == 'ticket'){
			saveTickets();
		}else if( $scop.gig_event.requisite == 'rsvp'){
			saveRsvp();
		}else{
			$scope.$emit('requisite.saved');
		}
	});

	$scope.$on('requisite.saved', function(){
		$http.post(url._api+'/classification/event-categories',{event_id: $scope.gig_event.id, categories: $scope.eventCategories})
			.success(function(res){
				if(res.success){
					$scope.$emit('event-categories.saved');
				}else{
					BSBoxService.notif.warning('Unable to save event categories.');
					savingState(false);
				}
			})
			.error(function(res){_ajaxError(res)});
	});

	$scope.$on('event-categories.saved', function(){
		alert('done');
	});
	var _ajaxError = function(res){
		console.log(res);
		savingState(false);
		BSBoxService.notif.error('Check network connection.');
	}

	angular.element('.organizer-search').typeahead(null,{
		templates:{
			empty:'<h5 class="pad-10">No Result found</h5>',
			suggestion:templates.suggestion_organizer
		},
		displayKey:function(user){return user.first_name+' '+user.last_name},
		source: function(q, callback){
			var notInId = [];
			for(var i =0; i < $scope.organizers.length; i++){
				notInId.push($scope.organizers[i].id);
			}
			$.ajax({
				url:url._api+'/contacts',
				data:{cmd:'search',q:q, not_in_id: notInId},
				success: function(res){
					callback(res.data)
				},
				error: function(res){
					console.log(res);
				}
			});
		}
	}).on('typeahead:autocompleted', addOrganizer).on('typeahead:selected', addOrganizer);

	var filewizard = new FileWizard('#filewizard',{
		url: url._api+'/upload/event-poster' ,
		method: 'post',
		paramName: 'img',
		multipleFiles: false,
		beforeFilesAdded: function(){
			this.resetFiles();
		},
		fileAdded: function(file){
			this.abort(); //abort existing upload
			angular.element('.fileward-wrapper .drop-title').html('Replace image');
			var fileReader = new FileReader;
			fileReader.addEventListener('load', function(e){
				angular.element('#filewizard .img-container img').attr('src', e.target.result);
			});
			fileReader.readAsDataURL(file);

			angular.element('.fileward-wrapper .progress .progress-bar').width(0);
			angular.element('.fileward-wrapper .progress').show();
			this.send();
			console.log(this.getFiles());
		},
		progress: function(percent){
			console.log(percent);
			angular.element('.fileward-wrapper .progress .progress-bar').width(percent+'%');
		},
		success: function(res){
			console.log(res);
			if(res.success){
				if( !$scope.gig_event )
					$scope.gig_event = {};

				$scope.gig_event.poster = res.data.filename;
			}
		},
		error: function(res){
			console.log(res);
		},
		complete: function(){

			angular.element('.fileward-wrapper .progress').hide();
		}
	});

	

});

angular.element(document).ready(function(){
	

	var editor = $('#gig_event_description').editable({
		inlineMode:false,
		useFrTag:false,
		buttons:["bold","italic","underline","strikeThrough","fontSize","fontFamily","color","sep","formatBlock","blockStyle","align","insertOrderedList","insertUnorderedList","outdent","indent","sep","createLink","insertImage","insertVideo","insertHorizontalRule","undo","redo"],
		imageUploadURL: url._api + '/upload/event-description-img',
		imageUploadParam: 'img'
	}).on('editable.imageError',function(e,m,res){
		bsbox.dialog({type:'danger',title:'Upload Error',message:'Error occured while uploading'})
		console.log(res);
	});
	

	//angular.element('.successive-dates-container').on('focus','.datetime', function(){
	//	console.log(this);
	//});
	

	angular.bootstrap(document, ['App']);


});

$(function(){
	$(window).on('resize', updateDropHandle);
	$('#filewizard .img-container img').on('load', updateDropHandle);


	$('.organizer-box').on('click', function(){
		angular.element('.organizer-search.tt-input').focus();
	});

	$('.successive-dates-container').on('focus','.datetime' ,function(){

		initDateTime(this);

	}).on('dp.change','.event-start',function(e){

		var i = $(this).index('.event-start');
		var $eventEnd = initDateTime($('.event-end').eq(i));
		$eventEnd.data('DateTimePicker').setMinDate(e.date.date( e.date.date() ));

	}).on('dp.change','.event-end', function(e){
		
		var i = $(this).index('.event-end');
		var $eventStart = initDateTime($('.event-start').eq(i));
		$eventStart.data('DateTimePicker').setMaxDate(e.date.date(  e.date.date() ) );

	});




	$('.ticket-body').on('focus','.sales-end,.sales-start', function(e){

		if( !$(this).data('DateTimePicker') ){
			initDateTime(this);
		}
			
		var picker = $(this).data('DateTimePicker'),
			//i = $(this).is('.sales-end') ? $(this).index('.sales-end') : $(this).index('.sales-start'),
			eventStartData =   $('.successive-dates-container .event-start').first().data('DateTimePicker'),
			eventEndData =  $('.successive-dates-container .event-end').last().data('DateTimePicker');
		
		if(eventStartData)
			picker.setMinDate( eventStartData.date );
		if(eventEndData){
			picker.setMaxDate( eventEndData.date );
		}

	});


GlobalStorage.store('event-form-validator', new labsValidator('event-form',{}));
	
});

function initDateTime(selector){
	var now = new Date(),
		minDate = new Date( now.getFullYear(), now.getMonth(), now.getDate()+1 );

	if( !$(selector).data('DateTimePicker') ){
		$(selector).datetimepicker({
			useCurrent:false,
			format:'MMM-DD-YYYY hh:mm A',
			minuteStepping:5,
			minDate: minDate
		});
	}
	return $(selector);
}
function updateDropHandle(){
	var h = $('.fileward-wrapper .img-container img').outerHeight();
	$('.fileward-wrapper .drop-handle-wrapper').height(h);
}



