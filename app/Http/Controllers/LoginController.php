<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Redirect;
use View;


class LoginController extends Controller{

	public function index(){
		return View::make('login.index');
	}

	public function store(){

		$auth = Auth::attempt(['email' => Input::get('email'),'password' => Input::get('password'),'verified' => true ]);

		if( $auth ){

			if( Input::get('_redirect') ) 
				return Redirect::to( Input::get('_redirect') );

			return Redirect::to('/');
		}

		return Redirect::route('login.index')->withInput( Input::all() )->withErrors(['error_msg' => 'Invalid Email/Password']);

	}
}