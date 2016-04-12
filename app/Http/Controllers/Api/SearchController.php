<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use Response;
use App\User;
use Auth;


use App\Helpers\Upload\Image;

class SearchController extends Controller
{

	public function postUsers()
	{
		
		$q = Input::get('q');
		$limit = Input::has('limit') ? Input::get('limit') : 10;
		$notInId = is_array(Input::get('not_in_id')) ? Input::get('not_in_id') : [];

		$users = User::whereRaw("( email LIKE '{$q}%' OR CONCAT(first_name,' ',last_name) LIKE '{$q}%' OR CONCAT(last_name,' ',first_name) LIKE '{$q}%' )")->where(['verified' => true])->whereNotIn('id',$notInId);
		if(Input::has('except_me'))
			$users->where('id','!=', Auth::id());

		$users = $users->get();
		$users->filter(function($user){

			$user->profile_img_info = Image::getImageLinks($user->profile_img, 'user_profile_img');
			return $user;
		});
		return $users;
	}
	
	public function getUsers()
	{
		return User::all();
	}
}