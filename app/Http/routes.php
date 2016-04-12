<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/login',[
	'as' => 'login',
	function(){
		return 'login';
	}
]);
*/

include 'routes-api.php';




Route::group(['middleware' => 'guest'], function(){
	Route::resource('signup','SignupController',['only' => ['index','store'] ]);
	Route::resource('_email-verification','EmailVerificationController',['only' => ['index','show'] ]);
	Route::resource('login','LoginController',['only' => ['index','store'] ]);
});



Route::group(['middleware' => 'auth'],function(){
	Route::resource('gig_group','GigGroupController',['except' => 'show']);
	Route::resource('events','GigEventController');
	Route::resource('settings','SettingsController');
});

Route::get('/',[
	'as' => 'home',
	'uses' => 'HomeController@index'
]);

Route::get('logout',[
	'as' => 'logout',
	function(){ Auth::logout(); return Redirect::to('/');}
]);

Route::resource('page','PageController',['only' => 'show']);
Route::resource('gig_group','GigGroupController',['only' => 'show']);
Route::resource('events','GigEventController',['only' => 'show']);
Route::get('users/{user_slug}',[
	'as' => 'user.show',
	'uses' => 'UserController@show'
]);

Route::controller('password','PasswordController');

Route::get('/test',[
	//'middleware' => 'auth',
	
	function(){

		$d = new \App\Models\EventDate;
		$d->fill([
			'timezone' => 'Asia/Manila',
			'start' => 'Mar-03-2016 7:00 AM'
		]);
		$d->save();
		return $d->toArray();
	}

]);

Route::get('/{user_slug}',[
	'as' => 'user_show',
	'uses' => 'UserController@show'
]);

