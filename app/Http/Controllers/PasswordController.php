<?php

namespace App\Http\Controllers;

use App\Models\Password\Resets;
use App\User;
use Mail;
use Input;
use Validator;
use View;
use Redirect;
use Str;
use Hash;
use Auth;

class PasswordController extends Controller
{

	public function getForgot()
	{
		if( Auth::check() )
			return Redirect::to('/');
		
		return View::make('password.forgot');
	}

	public function postForgot()
	{
		if( Auth::check() )
			return Redirect::to('/');

		$validator = Validator::make(Input::all(), ['email' => 'required|email|exists:user'],['email.exists' => 'The email doesn\'t exists.' ]);

		if( $validator->fails() )
			return Redirect::to('password/forgot')->withInput(Input::all())->withErrors($validator->messages() );

		$user = User::where(['email' => Input::get('email') ])->first();

		$reset = Resets::where(['user_id' => $user->id])->first();
		if( !$reset ){
			$reset = new Resets;
			$reset->user_id = $user->id;
		}

		$reset->token = $user->id .'-' . Str::random();

		if( $reset->save() ){
			$mail = Mail::send('emails.password.forgot',compact('reset'), function($msg) use($user) {
				$msg->to($user->email, $user->first_name)
					->subject('Password reset');
			});
			
			return View::make('password.forgot',['mail' => true]);
		}

	}

	public function getReset()
	{
		if( Auth::check() )
			return Redirect::to('/');

		$reset = Resets::where(['token' => Input::get('token')])->first();

		if( !$reset )
			return View::make('password.reset',['valid' => false]);

		return View::make('password.reset',['valid' => true,'reset' => $reset]);
	}

	public function postReset()
	{
		if( Auth::check() )
			return Redirect::to('/');

		$reset = Resets::where(['user_id' => Input::get('user_id'), 'token' => Input::get('token')])->first();

		if( !$reset )
			return View::make('password.reset',['valid' => false]);

		$validator = Validator::make(Input::all(), User::passwordRules(['current_password' => '']));

		if( $validator->fails() )
			return Redirect::to('password/reset?token='.Input::get('token'))->withErrors( $validator->messages() )->withInput( Input::all() );

		$user = User::find( $reset->user_id );
		$user->password = Hash::make( Input::get('new_password') );

		if( $user->save() ){
			$reset->delete();
			Auth::login($user);
			return Redirect::to($user->slug);
		}

		return View::make('errors.1');
	}
}