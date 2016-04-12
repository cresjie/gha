<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\User;
use Input;
use Auth;
use Response;
use App;
use Validator;
use Hash;
use Mail;

class PasswordController extends Controller
{

	public function update($cmd)
	{
		switch($cmd){
			case 'change':
				$validator = Validator::make(Input::all(), User::passwordRules());

				if( $validator->fails() )
					return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

				$user = Auth::user();
				if( !Hash::check(Input::get('current_password'), $user->password) )
					return Response::json(['success' => false, 'error_msg' => 'Invalid old password.']);

				$user->password = Hash::make( Input::get('new_password') );

				if( $user->save() ){
					Mail::send('emails.password.changed',[],function($msg){
						$msg->to(Auth::user()->email, Auth::user()->first_name)
							->subject('Password change');
					});
					return Response::json(['success' => true, 'data' => $user]);
				}

				return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);

				break;
			default:
				return App::abort(404);
		}
	}
}