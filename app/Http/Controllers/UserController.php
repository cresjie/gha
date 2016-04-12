<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Contact;
use View;
use Auth;


class UserController extends Controller
{
	
	public function show($slug)
	{
		$user = User::where(['slug' => $slug])->first();

		if( !$user ) return View::make('errors.404',[]);

		if( Auth::check() )
		{
			
			if( Auth::id() == $user->id )
				return View::make('users.show_admin',['user' => $user]);

			$contact = Contact::where(['user_id' => $user->id, 'requestor' => Auth::id() ])->orWhere(['user_id' => Auth::id(), 'requestor' => $user->id])->first();
			//return $user->id;
			return View::make('users.show_visitor',compact('user','contact'));
		}

		return View::make('users.show_guest',['user' => $user]);
	}
}