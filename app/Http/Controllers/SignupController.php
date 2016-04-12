<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\EmailVerification;
use App\Models\UserInfo;


//use App\Helpers\Generator;
use App\Helpers\Slug;

use Validator;
use Input;
use Redirect;
use Cookie;
use View;
use Hash;
use Crypt;
use Str;


class SignupController extends Controller{
	
	public function index(){
		return View::make('signup.index');
	}

	public function store()
	{

		$validator = Validator::make( Input::all(), User::createRules() );

		if( $validator->fails() )
			return Redirect::route('signup.index')->withInput( Input::all() )->withErrors( $validator->messages() );

		$user = new User;
		$user->fill( Input::all() );
		$user->password = Hash::make( Input::get('password') );
		$user->password_reminder = Crypt::encrypt( Input::get('password') );
		$user->slug = Slug::user($user->first_name);
		

		$user->save();

		$verification_token = $user->id . Str::random();
		
		/*
			in route:_email-verification cookie user_id  is used
			in order to retreive information and to send verification token to its email 

		*/

		Cookie::queue('user_id', $user->id, 20);

		EmailVerification::insert(['user_id' => $user->id,'verification_token' => $verification_token]);
		UserInfo::insert(['user_id' => $user->id]);

		return Redirect::route('_email-verification.index');
	}
}