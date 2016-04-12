<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\EmailVerification;
use Mail;
use Cookie;
use View;

class EmailVerificationController extends Controller{
	
	public function index()
	{

		

		if( Cookie::get('user_id') ){

			$userId = Cookie::get('user_id');
			$user = User::find( $userId );
			$verification = EmailVerification::find( $userId );

			if( !$verification )
				return View::make('email-verification.index',['mailResult' => null]);


			$mailResult =  Mail::send('emails.signup.verification',['verification_token' => $verification->verification_token],function($msg) use($user) {
				$msg->to( $user->email, $user->first_name)
					->subject('Gighubapp signup verification');
			});


			return View::make('email-verification.index',compact('mailResult') );
		}
		return View::make('email-verification.index',['mailResult' => null]);
	}

	/*
	public function show_($token)
	{

		$user = User::find( Cookie::get('user_id') );
		$verificaton = EmailVerification::where(['verification_token' => $token ])->first();

		$success = false;

		if( $user && $verification ){

			$user->verified = true;
			$user->save();
			$verification->delete();

			$success = true;
			return View::make('email-verification.show',compact('success'));

		}else if( $user && !$verification){
			
			if( $user->verified )
				return View::make('email-verification',['success' => $success, 'message' => 'User already verified']);
		
		}

		return View::make('email-verification.show',['success' => $success, 'message' => 'Token doesnt exists']);

		
	}
	*/

	
	public function show($token)
	{
		$success = false;
		$verification = EmailVerification::where(['verification_token' => $token])->first();

		if($verification){
			$user = User::find($verification->user_id);

			$user->verified = true;
			$user->save();
			$verification->delete();

			$success = true;
			return View::make('email-verification.show',compact('success'));
		}else{
			$userId = Cookie::get('user_id');
			if($userId){
				$user = User::find($userId);
				if($user->verified){
					return View::make('email-verification',['success' => $success, 'message' => 'User already verified']);
				}else{
					return View::make('email-verification',['success' => $success, 'message' => 'User is not verified.Please report this to <a href="mailto:accounts@gighubapp.com">accounts@gighubapp.com</a>']);
				}
			}
			return View::make('email-verification.show',['success' => $success, 'message' => 'Token doesnt exists']);
		}

		return View::make('email-verification.show',['success' => $success, 'message' => 'Token doesnt exists']);
	}
}