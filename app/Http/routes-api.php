<?php

Route::group(['prefix' => 'api','middleware' => 'auth'], function(){
	Route::get('/',['as' => 'api.index', function(){}]);

	Route::controller('search','Api\SearchController',['postUsers' => 'api.search.users']);
	Route::controller('slug','Api\SlugController',['anyIndex' => 'api.slug.index']);
	Route::controller('upload','Api\UploadController');

	Route::resource('gig_group','Api\GigGroupController');
	Route::resource('gig_group_members','Api\GigGroupMembersController');
	Route::resource('contacts','Api\ContactController');
	Route::resource('user','Api\UserController');
	Route::resource('password','Api\PasswordController');

	Route::resource('events','Api\GigEventController');
	Route::resource('event-dates','Api\EventDateController');
	Route::resource('event-tickets','Api\EventTicketController');
	Route::resource('event-organizers','Api\EventOrganizerController');
	Route::resource('event-rsvp','Api\EventRsvpController');
	
	Route::resource('geolocation/events','Api\Geolocation\EventController');

	Route::resource('classification/event-categories','Api\Classification\EventCategoryController');
});